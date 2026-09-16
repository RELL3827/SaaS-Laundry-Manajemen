<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Role;
use App\Models\Service;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles
        $ownerRole = Role::firstOrCreate(['name' => 'owner'], ['display_name' => 'Owner']);
        $cashierRole = Role::firstOrCreate(['name' => 'cashier'], ['display_name' => 'Kasir']);
        $staffRole = Role::firstOrCreate(['name' => 'staff'], ['display_name' => 'Staff Produksi']);

        // 2. Demo Tenant
        $tenant = Tenant::firstOrCreate(
            ['name' => 'Berkah Laundry Express'],
            [
                'company_name' => 'PT Berkah Laundry Sejahtera',
                'phone' => '08123456789',
                'address' => 'Jl. Mawar No. 12, Jakarta Selatan',
                'status' => 'active'
            ]
        );

        // 3. Demo User
        User::firstOrCreate(
            ['email' => 'owner@laundrypro.id'],
            [
                'name' => 'Hendro Wijaya',
                'password' => Hash::make('password123'),
                'tenant_id' => $tenant->id,
                'role_id' => $ownerRole->id
            ]
        );

        // 4. Default Services
        $services = [
            ['name' => 'Cuci + Setrika Reguler (2 Hari)', 'type' => 'kiloan', 'price' => 7000, 'unit' => 'kg', 'is_active' => true],
            ['name' => 'Cuci Kering Saja', 'type' => 'kiloan', 'price' => 5000, 'unit' => 'kg', 'is_active' => true],
            ['name' => 'Setrika Saja (Rapi)', 'type' => 'kiloan', 'price' => 4500, 'unit' => 'kg', 'is_active' => true],
            ['name' => 'Cuci Express Kilat (6 Jam)', 'type' => 'kiloan', 'price' => 12000, 'unit' => 'kg', 'is_active' => true],
            ['name' => 'Bed Cover Besar / King Size', 'type' => 'satuan', 'price' => 25000, 'unit' => 'pcs', 'is_active' => true],
            ['name' => 'Cuci Sepatu Sneaker', 'type' => 'satuan', 'price' => 30000, 'unit' => 'pasang', 'is_active' => true],
            ['name' => 'Jas / Blazer Formal', 'type' => 'satuan', 'price' => 35000, 'unit' => 'pcs', 'is_active' => true],
        ];

        foreach ($services as $svc) {
            Service::firstOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $svc['name']],
                $svc
            );
        }

        // 5. Demo Customers
        Customer::firstOrCreate(
            ['tenant_id' => $tenant->id, 'phone' => '081234567890'],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'address' => 'Jl. Anggrek No. 5, Jakarta',
                'points' => 15
            ]
        );

        $siti = Customer::firstOrCreate(
            ['tenant_id' => $tenant->id, 'phone' => '081987654321'],
            [
                'name' => 'Siti Rahma',
                'email' => 'siti@example.com',
                'address' => 'Apartemen Green View Lt. 8',
                'points' => 40
            ]
        );

        // 6. Demo Order for Tracking & Testing
        $budi = Customer::where('phone', '081234567890')->first();
        $owner = User::where('email', 'owner@laundrypro.id')->first();
        $reguler = Service::where('tenant_id', $tenant->id)->where('type', 'kiloan')->first();

        $demoOrder = \App\Models\Order::firstOrCreate(
            ['order_number' => 'LDR-20260915-0001'],
            [
                'tenant_id' => $tenant->id,
                'customer_id' => $budi?->id,
                'user_id' => $owner?->id,
                'status' => 'Diproses',
                'subtotal' => 21000,
                'discount' => 0,
                'total' => 21000,
                'payment_status' => 'Lunas',
                'notes' => 'Wangi Lavender, tolong lipat rapi'
            ]
        );

        if ($reguler && \App\Models\OrderItem::where('order_id', $demoOrder->id)->count() === 0) {
            \App\Models\OrderItem::create([
                'order_id' => $demoOrder->id,
                'service_id' => $reguler->id,
                'qty' => 3,
                'price' => $reguler->price,
                'subtotal' => 3 * $reguler->price,
            ]);
            \App\Models\Payment::create([
                'tenant_id' => $tenant->id,
                'order_id' => $demoOrder->id,
                'amount' => $demoOrder->total,
                'method' => 'Cash',
                'paid_at' => now(),
            ]);
        }
    }
}
