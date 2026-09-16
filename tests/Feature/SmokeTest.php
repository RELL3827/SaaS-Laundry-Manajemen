<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Role;
use App\Models\Service;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_app_pages_load(): void
    {
        $tenant = Tenant::create(['name' => 'Demo Laundry', 'phone' => '081234567890', 'address' => 'Jl. Merdeka No.1']);
        $role = Role::firstOrCreate(['name' => 'owner'], ['display_name' => 'Owner']);
        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'name' => 'Admin Demo',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $customer = Customer::create(['tenant_id' => $tenant->id, 'name' => 'Budi', 'phone' => '0812']);
        $service = Service::create(['tenant_id' => $tenant->id, 'name' => 'Cuci Komplit', 'type' => 'kiloan', 'price' => 7000, 'unit' => 'kg']);
        $order = Order::create([
            'tenant_id' => $tenant->id,
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'order_number' => 'LDR-20260915-SMOKE',
            'status' => 'Diterima',
            'subtotal' => 7000,
            'discount' => 0,
            'total' => 7000,
            'payment_status' => 'Lunas',
        ]);

        $this->actingAs($user);

        foreach (['/', '/dashboard', '/customers', '/services', '/orders', '/orders/create', "/orders/{$order->id}", "/track/{$order->order_number}"] as $url) {
            $response = $this->get($url);
            $response->assertStatus(200);
        }

        $dashboard = $this->get('/dashboard');
        $dashboard->assertSee('Laundry');
        $dashboard->assertSee('Pro');
        $dashboard->assertSee('bg-gradient-to-tr from-blue-600 to-indigo-600', false);
    }
}