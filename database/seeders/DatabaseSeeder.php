<?php

namespace Database\Seeders;

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
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@inq.test'],
            [
                'name' => 'Super Admin',
                'password' => 'password',
                'role' => 'superadmin',
                'email_verified_at' => now(),
            ]
        );

        $landlord = User::updateOrCreate(
            ['email' => 'arrendador@inq.test'],
            [
                'name' => 'Arrendador Demo',
                'password' => 'password',
                'role' => 'landlord',
                'phone' => '809-555-0000',
                'mora_type' => 'percent',
                'mora_value' => 5,
                'grace_days' => 5,
                'currency' => 'DOP',
                'email_verified_at' => now(),
            ]
        );

        auth()->login($landlord);

        $building = Building::create([
            'name' => 'Edificio Central',
            'address' => 'Av. Principal 100, Santo Domingo',
        ]);

        $apartments = collect([
            ['code' => 'A-101', 'label' => '1 habitación', 'base_rent' => 12000],
            ['code' => 'A-102', 'label' => '2 habitaciones', 'base_rent' => 18000],
            ['code' => 'A-103', 'label' => 'Estudio', 'base_rent' => 9000],
        ])->map(fn ($data) => Apartment::create([
            'building_id' => $building->id,
            'status' => 'available',
            ...$data,
        ]));

        $tenant = Tenant::create([
            'name' => 'Juan Pérez',
            'document' => '001-1234567-8',
            'phone' => '809-555-1234',
            'email' => 'juan@inq.test',
        ]);

        $lease = Lease::create([
            'apartment_id' => $apartments[0]->id,
            'tenant_id' => $tenant->id,
            'start_date' => now()->subMonths(2)->startOfMonth(),
            'rent_amount' => 12000,
            'deposit_amount' => 12000,
            'billing_day' => 5,
            'status' => 'active',
        ]);

        $apartments[0]->update(['status' => 'occupied']);

        $generator = app(ChargeGenerator::class);
        $recorder = app(PaymentRecorder::class);
        $receipts = app(ReceiptService::class);

        $lastMonth = now()->subMonth()->format('Y-m');
        $thisMonth = now()->format('Y-m');

        $generator->generateForLease($lease, $lastMonth);
        $generator->generateForLease($lease, $thisMonth, [
            ['concept' => 'Agua', 'amount' => 800],
            ['concept' => 'Energía', 'amount' => 1500],
        ]);

        $payment = $recorder->record([
            'landlord_id' => $landlord->id,
            'tenant_id' => $tenant->id,
            'lease_id' => $lease->id,
            'amount' => 12000,
            'method' => 'transfer',
            'reference' => 'TRF-0001',
            'paid_at' => now()->subMonth()->day(5)->toDateString(),
        ]);

        $receipts->issue($payment);
    }
}
