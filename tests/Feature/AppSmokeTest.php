<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Building;
use App\Models\Charge;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\User;
use App\Services\ChargeGenerator;
use App\Services\PaymentRecorder;
use App\Services\ReceiptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppSmokeTest extends TestCase
{
    use RefreshDatabase;

    private function landlord(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'landlord',
            'email_verified_at' => now(),
        ], $attributes));
    }

    private function scenario(User $landlord): array
    {
        $this->actingAs($landlord);

        $apartment = Apartment::create([
            'code' => 'A-101',
            'base_rent' => 10000,
            'status' => 'occupied',
        ]);

        $tenant = Tenant::create(['name' => 'Juan Pérez', 'phone' => '809-000-0000']);

        $lease = Lease::create([
            'apartment_id' => $apartment->id,
            'tenant_id' => $tenant->id,
            'start_date' => now()->subMonth(),
            'rent_amount' => 10000,
            'billing_day' => 5,
            'status' => 'active',
        ]);

        return compact('apartment', 'tenant', 'lease');
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_landlord_can_view_main_pages(): void
    {
        $landlord = $this->landlord();
        $this->scenario($landlord);

        foreach (['/dashboard', '/apartments', '/buildings', '/tenants', '/leases', '/leases/create', '/charges', '/payments', '/payments/create', '/expenses', '/reports', '/profile'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_charge_generation_creates_rent_and_services(): void
    {
        $landlord = $this->landlord();
        ['lease' => $lease] = $this->scenario($landlord);

        $period = now()->format('Y-m');
        app(ChargeGenerator::class)->generateForLease($lease, $period, [
            ['concept' => 'Agua', 'amount' => 500],
        ]);

        $this->assertDatabaseHas('charges', ['lease_id' => $lease->id, 'type' => 'rent', 'period' => $period]);
        $this->assertDatabaseHas('charges', ['lease_id' => $lease->id, 'type' => 'service', 'concept' => 'Agua']);
    }

    public function test_payment_is_allocated_and_receipt_issued(): void
    {
        $landlord = $this->landlord();
        ['tenant' => $tenant, 'lease' => $lease] = $this->scenario($landlord);

        $period = now()->format('Y-m');
        app(ChargeGenerator::class)->generateForLease($lease, $period);

        $payment = app(PaymentRecorder::class)->record([
            'landlord_id' => $landlord->id,
            'tenant_id' => $tenant->id,
            'lease_id' => $lease->id,
            'amount' => 10000,
            'method' => 'cash',
            'paid_at' => now()->toDateString(),
        ]);

        $charge = Charge::where('lease_id', $lease->id)->first();
        $this->assertEquals('paid', $charge->fresh()->status);

        app(ReceiptService::class)->issue($payment);
        $this->assertDatabaseHas('receipts', ['payment_id' => $payment->id, 'number' => 1]);

        $this->get('/payments/'.$payment->id.'/receipt')->assertOk();
    }

    public function test_payment_can_be_updated_and_realocated(): void
    {
        $landlord = $this->landlord();
        ['tenant' => $tenant, 'lease' => $lease] = $this->scenario($landlord);

        $period = now()->format('Y-m');
        app(ChargeGenerator::class)->generateForLease($lease, $period);

        $payment = app(PaymentRecorder::class)->record([
            'landlord_id' => $landlord->id,
            'tenant_id' => $tenant->id,
            'lease_id' => $lease->id,
            'amount' => 4000,
            'method' => 'cash',
            'paid_at' => now()->toDateString(),
        ]);
        app(ReceiptService::class)->issue($payment);

        $charge = Charge::where('lease_id', $lease->id)->firstOrFail();
        $this->assertEquals('partial', $charge->fresh()->status);
        $this->assertEquals(4000, (float) $charge->fresh()->paid_amount);

        $this->get('/payments/'.$payment->id.'/edit')->assertOk();

        $this->put('/payments/'.$payment->id, [
            'tenant_id' => $tenant->id,
            'amount' => 10000,
            'method' => 'transfer',
            'paid_at' => now()->toDateString(),
        ])->assertRedirect(route('payments.show', $payment));

        $this->assertEquals('paid', $charge->fresh()->status);
        $this->assertEquals(10000, (float) $charge->fresh()->paid_amount);
        $this->assertEquals(10000, (float) $payment->fresh()->receipt->total);
    }

    public function test_payment_can_be_voided_and_charges_reopened(): void
    {
        $landlord = $this->landlord();
        ['tenant' => $tenant, 'lease' => $lease] = $this->scenario($landlord);

        $period = now()->format('Y-m');
        app(ChargeGenerator::class)->generateForLease($lease, $period);

        $payment = app(PaymentRecorder::class)->record([
            'landlord_id' => $landlord->id,
            'tenant_id' => $tenant->id,
            'lease_id' => $lease->id,
            'amount' => 10000,
            'method' => 'cash',
            'paid_at' => now()->toDateString(),
        ]);
        app(ReceiptService::class)->issue($payment);

        $charge = Charge::where('lease_id', $lease->id)->firstOrFail();
        $this->assertEquals('paid', $charge->fresh()->status);
        $this->assertDatabaseHas('receipts', ['payment_id' => $payment->id]);

        $this->delete('/payments/'.$payment->id)->assertRedirect(route('payments.index'));

        $this->assertDatabaseMissing('payments', ['id' => $payment->id]);
        $this->assertDatabaseMissing('payment_allocations', ['payment_id' => $payment->id]);
        $this->assertDatabaseMissing('receipts', ['payment_id' => $payment->id]);
        $this->assertEquals('pending', $charge->fresh()->status);
        $this->assertEquals(0, (float) $charge->fresh()->paid_amount);
    }

    public function test_building_crud_and_delete_guard(): void
    {
        $landlord = $this->landlord();
        $this->actingAs($landlord);

        $this->post('/buildings', ['name' => 'Edificio Norte'])
            ->assertRedirect(route('buildings.index'));
        $this->assertDatabaseHas('buildings', ['landlord_id' => $landlord->id, 'name' => 'Edificio Norte']);

        $this->post('/buildings', ['name' => 'Edificio Sur']);
        $occupied = Building::where('name', 'Edificio Sur')->first();
        Apartment::create([
            'code' => 'B-1',
            'building_id' => $occupied->id,
            'base_rent' => 1000,
            'status' => 'available',
        ]);

        $this->delete("/buildings/{$occupied->id}")->assertRedirect(route('buildings.index'));
        $this->assertDatabaseHas('buildings', ['id' => $occupied->id]);

        $empty = Building::where('name', 'Edificio Norte')->first();
        $this->delete("/buildings/{$empty->id}")->assertRedirect(route('buildings.index'));
        $this->assertDatabaseMissing('buildings', ['id' => $empty->id]);
    }

    public function test_apartment_code_is_auto_generated(): void
    {
        $landlord = $this->landlord();
        $this->actingAs($landlord);

        $first = Apartment::create(['base_rent' => 1000, 'status' => 'available']);
        $second = Apartment::create(['base_rent' => 1000, 'status' => 'available']);

        $this->assertSame('APT-0001', $first->code);
        $this->assertSame('APT-0002', $second->code);

        $other = $this->landlord();
        $this->actingAs($other);
        $third = Apartment::create(['base_rent' => 1000, 'status' => 'available']);

        $this->assertSame('APT-0001', $third->code);
    }

    public function test_lease_syncs_apartment_status(): void
    {
        $landlord = $this->landlord();
        $this->actingAs($landlord);

        $apartment = Apartment::create(['base_rent' => 5000]);
        $tenant = Tenant::create(['name' => 'Ana Gómez']);

        $this->assertSame('available', $apartment->fresh()->status);

        $this->post('/leases', [
            'apartment_id' => $apartment->id,
            'tenant_id' => $tenant->id,
            'rent_amount' => 5000,
            'billing_day' => 10,
            'status' => 'active',
        ])->assertRedirect(route('leases.index'));

        $this->assertSame('occupied', $apartment->fresh()->status);

        $lease = Lease::where('apartment_id', $apartment->id)->firstOrFail();
        $this->delete('/leases/'.$lease->id)->assertRedirect(route('leases.index'));

        $this->assertSame('available', $apartment->fresh()->status);
    }

    public function test_landlords_cannot_see_each_others_data(): void
    {
        $a = $this->landlord();
        $this->scenario($a);

        $b = $this->landlord();
        $this->actingAs($b);

        $this->assertEquals(0, Apartment::count());
        $this->assertEquals(0, Tenant::count());
    }

    public function test_superadmin_cannot_access_landlord_area(): void
    {
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)->get('/dashboard')->assertRedirect(route('admin.dashboard'));
        $this->actingAs($admin)->get('/admin')->assertOk();
    }
}
