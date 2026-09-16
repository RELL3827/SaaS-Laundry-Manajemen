<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Role;
use App\Models\Service;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function userWithTenant(): array
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

        return [$tenant, $user];
    }

    public function test_print_and_track_routes(): void
    {
        [$tenant, $user] = $this->userWithTenant();

        $customer = Customer::create(['tenant_id' => $tenant->id, 'name' => 'Budi', 'phone' => '0812']);
        $service = Service::create(['tenant_id' => $tenant->id, 'name' => 'Cuci Komplit', 'type' => 'kiloan', 'price' => 7000, 'unit' => 'kg']);
        $order = Order::create([
            'tenant_id' => $tenant->id,
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'order_number' => 'LDR-20260915-TEST',
            'status' => 'Diterima',
            'subtotal' => 14000,
            'discount' => 0,
            'total' => 14000,
            'payment_status' => 'Lunas',
        ]);
        OrderItem::create(['order_id' => $order->id, 'service_id' => $service->id, 'qty' => 2, 'price' => 7000, 'subtotal' => 14000]);

        $this->actingAs($user)->get("/orders/{$order->id}/print")->assertStatus(200);
        $this->get("/track/{$order->order_number}")->assertStatus(200);
    }

    public function test_customers_component_flow(): void
    {
        [$tenant, $user] = $this->userWithTenant();
        $this->actingAs($user);

        Livewire::test('customers')
            ->set('name', 'Siti')
            ->set('phone', '0812345')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('customers', ['name' => 'Siti', 'tenant_id' => $tenant->id]);
    }

    public function test_services_component_flow(): void
    {
        [$tenant, $user] = $this->userWithTenant();
        $this->actingAs($user);

        Livewire::test('services')
            ->set('name', 'Cuci Express')
            ->set('type', 'express')
            ->set('price', 15000)
            ->set('unit', 'kg')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('services', ['name' => 'Cuci Express', 'tenant_id' => $tenant->id]);
    }

    public function test_dashboard_component_renders(): void
    {
        [$tenant, $user] = $this->userWithTenant();
        $this->actingAs($user);

        Livewire::test('dashboard')->assertOk();
    }

    public function test_order_creation_component(): void
    {
        [$tenant, $user] = $this->userWithTenant();
        $this->actingAs($user);

        $customer = Customer::create(['tenant_id' => $tenant->id, 'name' => 'Budi', 'phone' => '0812']);
        $service = Service::create(['tenant_id' => $tenant->id, 'name' => 'Cuci Komplit', 'type' => 'kiloan', 'price' => 7000, 'unit' => 'kg']);

        Livewire::test('orders.create')
            ->set('searchCustomer', 'Budi')
            ->assertOk();

        Livewire::test('orders.create')
            ->call('selectCustomer', $customer->id)
            ->call('addToCart', $service->id)
            ->set('notes', 'Test note')
            ->call('goToPayment')
            ->set('paymentMethod', 'Cash')
            ->set('amountPaid', 7000)
            ->call('processOrder')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('orders', ['notes' => 'Test note', 'tenant_id' => $tenant->id]);
        $this->assertDatabaseHas('payments', ['amount' => 7000, 'method' => 'Cash']);
    }
}