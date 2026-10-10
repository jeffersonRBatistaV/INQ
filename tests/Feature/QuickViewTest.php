<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Building;
use App\Models\Charge;
use App\Models\Lease;
use App\Models\Tenant;
use App\Models\User;
use App\Services\ChargeGenerator;
use App\Services\PaymentRecorder;
use App\Services\ReceiptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuickViewTest extends TestCase
{
    use RefreshDatabase;

    private function landlord(): User
    {
        return User::factory()->create([
            'role' => 'landlord',
            'email_verified_at' => now(),
        ]);
    }

    private function scenario(User $landlord): array
    {
        $this->actingAs($landlord);

        $building = Building::create(['name' => 'Edificio Central', 'address' => 'Av. Principal']);
        $apartment = Apartment::create(['code' => 'A-101', 'base_rent' => 10000, 'status' => 'occupied', 'building_id' => $building->id]);
        $tenant = Tenant::create(['name' => 'Juan Pérez', 'phone' => '809-000-0000']);
        $lease = Lease::create([
            'apartment_id' => $apartment->id,
            'tenant_id' => $tenant->id,
            'rent_amount' => 10000,
            'billing_day' => 5,
            'status' => 'active',
        ]);

        app(ChargeGenerator::class)->generateForLease($lease, now()->format('Y-m'));

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

        return compact('building', 'apartment', 'tenant', 'lease', 'charge', 'payment');
    }

    public function test_tenant_panel_renders(): void
    {
        $landlord = $this->landlord();
        ['tenant' => $tenant] = $this->scenario($landlord);

        $this->get('/quick/tenants/'.$tenant->id)
            ->assertOk()
            ->assertSee('Juan Pérez')
            ->assertSee('Contrato activo');
    }

    public function test_apartment_panel_renders(): void
    {
        $landlord = $this->landlord();
        ['apartment' => $apartment] = $this->scenario($landlord);

        $this->get('/quick/apartments/'.$apartment->id)
            ->assertOk()
            ->assertSee('A-101')
            ->assertSee('Edificio Central');
    }

    public function test_building_panel_renders(): void
    {
        $landlord = $this->landlord();
        ['building' => $building] = $this->scenario($landlord);

        $this->get('/quick/buildings/'.$building->id)
            ->assertOk()
            ->assertSee('Edificio Central')
            ->assertSee('Ocupación');
    }

    public function test_lease_panel_renders(): void
    {
        $landlord = $this->landlord();
        ['lease' => $lease] = $this->scenario($landlord);

        $this->get('/quick/leases/'.$lease->id)
            ->assertOk()
            ->assertSee('Cargos generados');
    }

    public function test_charge_panel_shows_applied_payments(): void
    {
        $landlord = $this->landlord();
        ['charge' => $charge] = $this->scenario($landlord);

        $this->get('/quick/charges/'.$charge->id)
            ->assertOk()
            ->assertSee('Pagos aplicados')
            ->assertSee('4,000.00');
    }

    public function test_payment_panel_shows_applied_charges(): void
    {
        $landlord = $this->landlord();
        ['payment' => $payment] = $this->scenario($landlord);

        $this->get('/quick/payments/'.$payment->id)
            ->assertOk()
            ->assertSee('Aplicado a cargos');
    }

    public function test_landlord_cannot_view_other_landlords_panel(): void
    {
        $a = $this->landlord();
        ['tenant' => $tenant] = $this->scenario($a);

        $b = $this->landlord();
        $this->actingAs($b);

        $this->get('/quick/tenants/'.$tenant->id)->assertNotFound();
    }
}
