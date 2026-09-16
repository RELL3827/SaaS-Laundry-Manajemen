<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Service;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderTrackingAndStatusTest extends TestCase
{
    use RefreshDatabase;

    private function setupOrder(): array
    {
        $tenant = Tenant::create(['name' => 'Laundry Bintang', 'phone' => '0812999888', 'address' => 'Jl. Mawar No. 12']);
        $role = Role::firstOrCreate(['name' => 'owner'], ['display_name' => 'Owner']);
        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'name' => 'Owner Bintang',
            'email' => 'owner@bintang.com',
            'password' => bcrypt('secret123'),
        ]);

        $customer = Customer::create([
            'tenant_id' => $tenant->id,
            'name' => 'Rina Wijaya',
            'phone' => '085712345678',
        ]);

        $service = Service::create([
            'tenant_id' => $tenant->id,
            'name' => 'Cuci Kering Setrika',
            'type' => 'kiloan',
            'price' => 8000,
            'unit' => 'kg',
        ]);

        $order = Order::create([
            'tenant_id' => $tenant->id,
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'order_number' => 'LDR-20260916-TEST',
            'status' => 'Diterima',
            'subtotal' => 24000,
            'discount' => 0,
            'total' => 24000,
            'payment_status' => 'Belum Bayar',
            'notes' => 'Pisahkan baju putih',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'service_id' => $service->id,
            'qty' => 3,
            'price' => 8000,
            'subtotal' => 24000,
        ]);

        return [$tenant, $user, $customer, $service, $order];
    }

    public function test_owner_can_advance_order_status_step_by_step(): void
    {
        [$tenant, $user, $customer, $service, $order] = $this->setupOrder();
        $this->actingAs($user);

        $component = Livewire::test('orders.show', ['id' => $order->id]);
        
        // Step 1: Diterima -> Dicuci
        $component->call('advanceStatus');
        $this->assertEquals('Dicuci', $order->fresh()->status);

        // Step 2: Dicuci -> Dikeringkan
        $component->call('advanceStatus');
        $this->assertEquals('Dikeringkan', $order->fresh()->status);

        // Step 3: Dikeringkan -> Disetrika
        $component->call('advanceStatus');
        $this->assertEquals('Disetrika', $order->fresh()->status);

        // Step 4: Disetrika -> Selesai
        $component->call('advanceStatus');
        $this->assertEquals('Selesai', $order->fresh()->status);

        // Step 5: Selesai -> Diambil
        $component->call('advanceStatus');
        $this->assertEquals('Diambil', $order->fresh()->status);
    }

    public function test_owner_can_update_status_directly(): void
    {
        [$tenant, $user, $customer, $service, $order] = $this->setupOrder();
        $this->actingAs($user);

        // Test in orders.show
        Livewire::test('orders.show', ['id' => $order->id])
            ->call('updateStatus', 'Selesai')
            ->assertHasNoErrors();

        $this->assertEquals('Selesai', $order->fresh()->status);

        // Test in orders.index
        Livewire::test('orders.index')
            ->call('updateStatus', $order->id, 'Disetrika')
            ->assertHasNoErrors();

        $this->assertEquals('Disetrika', $order->fresh()->status);
    }

    public function test_owner_can_mark_order_as_paid(): void
    {
        [$tenant, $user, $customer, $service, $order] = $this->setupOrder();
        $this->actingAs($user);

        Livewire::test('orders.show', ['id' => $order->id])
            ->call('markAsPaid')
            ->assertHasNoErrors();

        $fresh = $order->fresh();
        $this->assertEquals('Lunas', $fresh->payment_status);
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'amount' => 24000,
            'method' => 'Cash',
        ]);
    }

    public function test_owner_can_delete_order_safely(): void
    {
        [$tenant, $user, $customer, $service, $order] = $this->setupOrder();
        $this->actingAs($user);

        Livewire::test('orders.index')
            ->call('deleteOrder', $order->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
        $this->assertDatabaseMissing('order_items', ['order_id' => $order->id]);
    }

    public function test_public_tracking_page_renders_without_authentication(): void
    {
        [$tenant, $user, $customer, $service, $order] = $this->setupOrder();

        $response = $this->get("/track/{$order->order_number}");
        $response->assertStatus(200);
        $response->assertSee($order->order_number);
        $response->assertSee('Rina Wijaya');
        $response->assertSee('Cuci Kering Setrika');
        $response->assertSee('Diterima');
    }

    public function test_login_and_register_pages_have_back_button(): void
    {
        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('Kembali ke Beranda Utama');

        $registerResponse = $this->get('/register');
        $registerResponse->assertStatus(200);
        $registerResponse->assertSee('Kembali ke Beranda Utama');
    }
}
