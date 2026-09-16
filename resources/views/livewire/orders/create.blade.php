<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

new #[Layout('components.layouts.app')] class extends Component
{
    public $step = 1;
    
    // Step 1: Customer
    public $searchCustomer = '';
    public $selectedCustomerId = null;
    public $newCustomerName = '';
    public $newCustomerPhone = '';

    // Step 2: Services
    public $cart = [];
    public $notes = '';
    
    // Step 3: Payment
    public $paymentMethod = 'Cash';
    public $amountPaid = 0;
    
    // Step 4: Success
    public $createdOrderId = null;
    public $createdOrderNumber = '';

    // Computed properties
    public function with()
    {
        return [
            'customers' => $this->searchCustomer 
                ? Customer::where(function($q) {
                      $q->where('name', 'like', '%'.$this->searchCustomer.'%')
                        ->orWhere('phone', 'like', '%'.$this->searchCustomer.'%');
                  })->limit(5)->get()
                : collect(),
            'services' => Service::where('is_active', true)->get(),
        ];
    }

    public function selectCustomer($id)
    {
        $this->selectedCustomerId = $id;
        $this->step = 2;
    }

    public function createCustomer()
    {
        $this->validate([
            'newCustomerName' => 'required|string|max:255',
            'newCustomerPhone' => 'nullable|string|max:20',
        ]);

        $customer = Customer::create([
            'name' => $this->newCustomerName,
            'phone' => $this->newCustomerPhone,
        ]);

        $this->selectedCustomerId = $customer->id;
        $this->step = 2;
    }

    public function addToCart($serviceId)
    {
        $service = Service::find($serviceId);
        if (!$service) return;

        $existingKey = null;
        foreach ($this->cart as $key => $item) {
            if ($item['service_id'] == $serviceId) {
                $existingKey = $key;
                break;
            }
        }

        if ($existingKey !== null) {
            $this->cart[$existingKey]['qty'] += 1;
            $this->cart[$existingKey]['subtotal'] = $this->cart[$existingKey]['qty'] * $this->cart[$existingKey]['price'];
        } else {
            $this->cart[] = [
                'service_id' => $service->id,
                'name' => $service->name,
                'type' => $service->type,
                'price' => $service->price,
                'unit' => $service->unit,
                'qty' => 1,
                'subtotal' => $service->price
            ];
        }
    }

    public function updateQty($key, $qty)
    {
        if ($qty <= 0) {
            unset($this->cart[$key]);
            $this->cart = array_values($this->cart);
        } else {
            $this->cart[$key]['qty'] = $qty;
            $this->cart[$key]['subtotal'] = $qty * $this->cart[$key]['price'];
        }
    }

    public function removeFromCart($key)
    {
        unset($this->cart[$key]);
        $this->cart = array_values($this->cart); // re-index
    }

    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum('subtotal');
    }

    public function getTotalProperty()
    {
        // For MVP, total = subtotal
        return $this->getSubtotalProperty();
    }

    public function goToPayment()
    {
        if (count($this->cart) == 0) {
            $this->addError('cart', 'Pilih minimal satu layanan');
            return;
        }
        $this->amountPaid = $this->getTotalProperty();
        $this->step = 3;
    }

    public function processOrder()
    {
        $tenant = auth()->user()->tenant;
        if ($tenant && !$tenant->canCreateOrder()) {
            $this->addError('paymentMethod', 'Kuota pesanan bulan ini untuk Paket Gratis telah mencapai batas (' . $tenant->getOrderLimit() . ' order). Silakan upgrade ke Paket Pro.');
            return;
        }

        $this->validate([
            'paymentMethod' => 'required|string',
            'amountPaid' => 'required|numeric|min:0'
        ]);

        DB::transaction(function () {
            $orderNumber = 'LDR-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            
            $total = $this->getTotalProperty();
            $paymentStatus = 'Belum Bayar';
            if ($this->amountPaid >= $total) {
                $paymentStatus = 'Lunas';
            } elseif ($this->amountPaid > 0) {
                $paymentStatus = 'DP';
            }

            $order = Order::create([
                'customer_id' => $this->selectedCustomerId,
                'user_id' => auth()->id(),
                'order_number' => $orderNumber,
                'status' => 'Diterima',
                'subtotal' => $this->getSubtotalProperty(),
                'discount' => 0,
                'total' => $total,
                'payment_status' => $paymentStatus,
                'notes' => $this->notes,
            ]);

            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'service_id' => $item['service_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            if ($this->amountPaid > 0) {
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => $this->amountPaid,
                    'method' => $this->paymentMethod
                ]);
            }

            $this->createdOrderId = $order->id;
            $this->createdOrderNumber = $order->order_number;
            $this->step = 4;
        });
    }

    public function goBack()
    {
        if ($this->step > 1 && $this->step < 4) {
            $this->step--;
        }
    }
    
    public function resetAll()
    {
        $this->step = 1;
        $this->searchCustomer = '';
        $this->selectedCustomerId = null;
        $this->cart = [];
        $this->notes = '';
        $this->amountPaid = 0;
        $this->createdOrderId = null;
    }
};
?>

@php
    $tenant = auth()->user()->tenant;
    $isPro = $tenant?->isPro() ?? false;
    $canCreate = $tenant?->canCreateOrder() ?? true;
    $monthlyUsed = $tenant?->getMonthlyOrdersCount() ?? 0;
    $orderLimit = $tenant?->getOrderLimit() ?? 30;
@endphp

<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-gray-900">Buat Order Baru</h1>
            @if(!$isPro)
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $canCreate ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                    Kuota Bulan Ini: {{ $monthlyUsed }} / {{ $orderLimit }} Nota
                </span>
            @else
                <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-900 border border-amber-300 flex items-center gap-1">
                    👑 Unlimited Order (Pro)
                </span>
            @endif
        </div>

        <div class="flex items-center gap-3">
            @if(!$isPro && !$canCreate)
                <button @click="showUpgradeModal = true" type="button" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-400 hover:bg-amber-300 text-slate-900 shadow-xs transition-colors">
                    Upgrade ke Pro ⚡
                </button>
            @endif

            @if($step > 1 && $step < 4)
                <button wire:click="goBack" class="text-gray-600 hover:text-gray-900 flex items-center text-sm font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </button>
            @endif
        </div>
    </div>

    @if(!$canCreate)
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-lg shrink-0">
                    ⚠️
                </div>
                <div>
                    <p class="font-extrabold text-rose-900 text-sm">Batas Kuota 30 Order Bulan Ini Telah Habis!</p>
                    <p class="text-xs text-rose-700 mt-0.5">Anda menggunakan Paket Starter Gratis. Upgrade ke Paket Pro untuk kuota tanpa batas dan fitur WhatsApp otomatis.</p>
                </div>
            </div>
            <button @click="showUpgradeModal = true" type="button" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-bold text-xs shadow-md shrink-0 transition-all">
                Aktifkan Unlimited Order ⚡
            </button>
        </div>
    @endif

    <!-- Stepper UI -->
    <div class="mb-8">
        <div class="flex items-center justify-between w-full relative">
            <div class="absolute left-0 top-1/2 transform -translate-y-1/2 w-full h-1 bg-gray-200 rounded"></div>
            <div class="absolute left-0 top-1/2 transform -translate-y-1/2 h-1 bg-blue-600 rounded transition-all duration-300" style="width: {{ ($step - 1) * 33.33 }}%"></div>
            
            @foreach(['Customer', 'Layanan', 'Pembayaran', 'Selesai'] as $index => $label)
            <div class="relative flex flex-col items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm z-10 transition-colors {{ $step >= ($index + 1) ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-200 text-gray-500' }}">
                    {{ $index + 1 }}
                </div>
                <div class="absolute top-10 text-xs font-medium {{ $step >= ($index + 1) ? 'text-blue-600' : 'text-gray-500' }}">{{ $label }}</div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="mt-12 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        
        <!-- STEP 1: CUSTOMER -->
        @if($step == 1)
        <div>
            <h2 class="text-lg font-medium text-gray-900 mb-4">Pilih atau Tambah Customer</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Cari Customer -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Customer Lama</label>
                    <input wire:model.live.debounce.300ms="searchCustomer" type="text" placeholder="Masukkan nama atau nomor HP..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-3 border">
                    
                    @if($searchCustomer)
                    <div class="mt-2 border border-gray-200 rounded-md overflow-hidden bg-white shadow-sm">
                        @forelse($customers as $c)
                            <div wire:click="selectCustomer({{ $c->id }})" class="p-3 border-b border-gray-100 hover:bg-blue-50 cursor-pointer flex justify-between items-center">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $c->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $c->phone ?? 'Tidak ada no. HP' }}</div>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                        @empty
                            <div class="p-3 text-sm text-gray-500 text-center">Customer tidak ditemukan</div>
                        @endforelse
                    </div>
                    @endif
                </div>

                <!-- Tambah Customer -->
                <div class="border-t md:border-t-0 md:border-l border-gray-200 pt-6 md:pt-0 md:pl-8">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Atau Tambah Customer Baru</label>
                    <div class="space-y-4">
                        <div>
                            <input wire:model="newCustomerName" type="text" placeholder="Nama Lengkap" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-3 border">
                            @error('newCustomerName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <input wire:model="newCustomerPhone" type="text" placeholder="Nomor HP / WhatsApp" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-3 border">
                            @error('newCustomerPhone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <button wire:click="createCustomer" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Simpan & Lanjut
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- STEP 2: SERVICES -->
        @if($step == 2)
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Pilihan Layanan -->
            <div class="md:w-1/2">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Pilih Layanan</h2>
                <div class="grid grid-cols-2 gap-3 max-h-96 overflow-y-auto pr-2">
                    @foreach($services as $service)
                        <div wire:click="addToCart({{ $service->id }})" class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-colors">
                            <div class="font-medium text-gray-900">{{ $service->name }}</div>
                            <div class="text-sm text-gray-500 mt-1">Rp {{ number_format($service->price, 0, ',', '.') }} / {{ $service->unit }}</div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan (Opsional)</label>
                    <textarea wire:model="notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border" placeholder="Contoh: Jangan disetrika terlalu panas..."></textarea>
                </div>
            </div>

            <!-- Keranjang / Timbangan -->
            <div class="md:w-1/2 bg-gray-50 rounded-xl p-6 border border-gray-200">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Keranjang Order</h2>
                
                @error('cart')
                    <div class="mb-4 bg-red-50 text-red-700 p-3 rounded text-sm">{{ $message }}</div>
                @enderror

                @if(count($cart) > 0)
                    <div class="space-y-3 mb-6 max-h-64 overflow-y-auto">
                        @foreach($cart as $key => $item)
                        <div class="flex justify-between items-center bg-white p-3 rounded-lg shadow-sm border border-gray-100">
                            <div class="flex-1">
                                <div class="font-medium text-sm">{{ $item['name'] }}</div>
                                <div class="text-xs text-gray-500">Rp {{ number_format($item['price'], 0, ',', '.') }} / {{ $item['unit'] }}</div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="number" step="0.1" wire:model.live.debounce.500ms="cart.{{ $key }}.qty" wire:change="updateQty({{ $key }}, $event.target.value)" class="w-16 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-1 border text-center" min="0">
                                <span class="text-sm text-gray-500 w-6">{{ $item['unit'] }}</span>
                                <div class="w-24 text-right font-medium text-sm">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</div>
                                <button wire:click="removeFromCart({{ $key }})" class="text-red-500 hover:text-red-700 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-200 pt-4 flex justify-between items-center mb-6">
                        <div class="text-lg font-bold text-gray-900">Total</div>
                        <div class="text-2xl font-bold text-blue-600">Rp {{ number_format($this->total, 0, ',', '.') }}</div>
                    </div>

                    <button wire:click="goToPayment" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Lanjut Pembayaran
                    </button>
                @else
                    <div class="text-center py-12 text-gray-400">
                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <p>Keranjang kosong</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- STEP 3: PAYMENT -->
        @if($step == 3)
        <div class="max-w-2xl mx-auto">
            <h2 class="text-lg font-medium text-gray-900 mb-6 text-center">Detail Pembayaran</h2>
            
            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 mb-6">
                <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-4">
                    <div class="text-gray-500">Total Tagihan</div>
                    <div class="text-3xl font-bold text-gray-900">Rp {{ number_format($this->total, 0, ',', '.') }}</div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                        <select wire:model="paymentMethod" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border bg-white">
                            <option value="Cash">Cash (Tunai)</option>
                            <option value="Transfer">Transfer Bank</option>
                            <option value="QRIS">QRIS</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Dibayar (Rp)</label>
                        <input wire:model="amountPaid" type="number" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border font-bold text-lg" min="0">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan atau isi 0 jika pelanggan belum membayar (Tagihan).</p>
                    </div>
                </div>
            </div>

            @if($canCreate)
                <button wire:click="processOrder" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-md text-lg font-bold text-white bg-green-600 hover:bg-green-700 active:scale-98 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all">
                    Proses Order
                </button>
            @else
                <div class="p-4 bg-rose-50 border-2 border-dashed border-rose-300 rounded-2xl text-center space-y-2">
                    <p class="font-extrabold text-rose-900 text-base">⚠️ Kuota 30 Order Bulan Ini Telah Tercapai</p>
                    <p class="text-xs text-rose-700">Anda tidak dapat memproses pesanan baru dengan Paket Starter. Tingkatkan akun ke Pro sekarang.</p>
                    <button @click="showUpgradeModal = true" type="button" class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-extrabold text-sm rounded-xl shadow-md hover:from-blue-700 hover:to-indigo-700 transition-all">
                        Upgrade ke Paket Pro Sekarang ⚡
                    </button>
                </div>
            @endif
        </div>
        @endif

        <!-- STEP 4: SUCCESS -->
        @if($step == 4)
        <div class="text-center py-8">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
                <svg class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            
            <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Order Berhasil Dibuat!</h2>
            <p class="text-lg text-gray-500 mb-6">Nomor Order: <span class="font-bold text-gray-900">{{ $createdOrderNumber }}</span></p>

            <div class="flex flex-col sm:flex-row justify-center gap-3 mt-8">
                <a href="/orders/{{ $createdOrderId }}" class="inline-flex justify-center items-center py-3 px-6 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    Lihat & Lacak Status
                </a>

                <a href="/orders/{{ $createdOrderId }}/print" target="_blank" class="inline-flex justify-center items-center py-3 px-6 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Nota
                </a>
                
                <button wire:click="resetAll" class="inline-flex justify-center items-center py-3 px-6 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Buat Order Baru
                </button>
            </div>
        </div>
        @endif
    </div>
</div>
