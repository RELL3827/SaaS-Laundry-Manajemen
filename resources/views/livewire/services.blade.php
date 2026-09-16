<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Service;

new #[Layout('components.layouts.app')] class extends Component
{
    use WithPagination;

    public $search = '';
    public $isModalOpen = false;
    
    // Form fields
    public $serviceId = null;
    public $name = '';
    public $type = 'kiloan';
    public $price = '';
    public $unit = 'kg';
    public $is_active = true;

    public function with()
    {
        return [
            'services' => Service::where('name', 'like', '%' . $this->search . '%')
                            ->orderBy('created_at', 'desc')
                            ->paginate(10)
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    public function resetForm()
    {
        $this->serviceId = null;
        $this->name = '';
        $this->type = 'kiloan';
        $this->price = '';
        $this->unit = 'kg';
        $this->is_active = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
            'is_active' => 'boolean'
        ]);

        if ($this->serviceId) {
            $service = Service::findOrFail($this->serviceId);
            $service->update([
                'name' => $this->name,
                'type' => $this->type,
                'price' => $this->price,
                'unit' => $this->unit,
                'is_active' => $this->is_active
            ]);
            session()->flash('success', 'Layanan berhasil diperbarui!');
        } else {
            Service::create([
                'name' => $this->name,
                'type' => $this->type,
                'price' => $this->price,
                'unit' => $this->unit,
                'is_active' => $this->is_active
            ]);
            session()->flash('success', 'Layanan baru berhasil ditambahkan!');
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);
        $this->serviceId = $service->id;
        $this->name = $service->name;
        $this->type = $service->type;
        $this->price = $service->price;
        $this->unit = $service->unit;
        $this->is_active = $service->is_active;
        
        $this->isModalOpen = true;
    }

    public function toggleActive($id)
    {
        $service = Service::findOrFail($id);
        $service->update(['is_active' => !$service->is_active]);
        session()->flash('success', 'Status layanan ' . $service->name . ' diubah!');
    }

    public function delete($id)
    {
        Service::findOrFail($id)->delete();
        session()->flash('success', 'Layanan berhasil dihapus!');
    }
};
?>

<div>
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Layanan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Atur paket cucian, tarif harga kiloan/satuan, dan status layanan</p>
        </div>
        <button wire:click="openModal" wire:loading.attr="disabled" type="button" class="bg-blue-600 hover:bg-blue-700 active:bg-blue-800 disabled:opacity-60 text-white px-4 py-2.5 rounded-xl shadow-sm hover:shadow font-medium text-sm flex items-center gap-2 transition-all">
            <svg wire:loading.remove wire:target="openModal" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <svg wire:loading wire:target="openModal" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span wire:loading.remove wire:target="openModal">Tambah Layanan</span>
            <span wire:loading wire:target="openModal">Membuka Form...</span>
        </button>
    </div>

    <!-- Flash Message -->
    @if(session()->has('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-sm font-semibold">&times;</button>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50/50">
            <div class="relative w-full md:w-80">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama layanan..." class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-300 shadow-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm bg-white">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Layanan</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori / Tipe</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tarif Harga</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($services as $service)
                    <tr class="hover:bg-gray-50/75 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">{{ $service->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-slate-100 text-slate-800 capitalize border border-slate-200/60">
                                {{ $service->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">Rp {{ number_format($service->price, 0, ',', '.') }} <span class="text-xs text-gray-500 font-normal">/ {{ $service->unit }}</span></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button wire:click="toggleActive({{ $service->id }})" wire:loading.attr="disabled" title="Klik untuk ubah status" class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full cursor-pointer transition-colors disabled:opacity-50 {{ $service->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-200/60 hover:bg-rose-100' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 self-center {{ $service->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                {{ $service->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="edit({{ $service->id }})" wire:loading.attr="disabled" class="text-blue-600 hover:text-blue-800 disabled:opacity-50 font-semibold mr-3 transition-colors">
                                <span wire:loading.remove wire:target="edit({{ $service->id }})">Edit</span>
                                <span wire:loading wire:target="edit({{ $service->id }})">...</span>
                            </button>
                            <button wire:click="delete({{ $service->id }})" wire:confirm="Yakin ingin menghapus layanan ini?" wire:loading.attr="disabled" class="text-rose-600 hover:text-rose-800 disabled:opacity-50 font-semibold transition-colors">
                                <span wire:loading.remove wire:target="delete({{ $service->id }})">Hapus</span>
                                <span wire:loading wire:target="delete({{ $service->id }})">...</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <p class="font-medium text-gray-600">Belum ada data layanan</p>
                            <p class="text-xs text-gray-400 mt-1">Klik tombol "Tambah Layanan" untuk mulai menambahkan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200">
            {{ $services->links() }}
        </div>
    </div>

    <!-- Modal Form (Fixed Z-Index & Backdrop) -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 min-h-screen" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" wire:click="closeModal"></div>

        <!-- Modal Dialog Box -->
        <div class="relative z-10 w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100 transform transition-all my-8">
            <form wire:submit="save">
                <div class="bg-white px-6 pt-6 pb-5">
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900" id="modal-title">
                                    {{ $serviceId ? 'Edit Layanan' : 'Tambah Layanan Baru' }}
                                </h3>
                                <p class="text-xs text-gray-500">Tentukan nama, jenis, dan harga tarif layanan</p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Layanan <span class="text-rose-500">*</span></label>
                            <input wire:model="name" type="text" class="w-full rounded-lg border border-gray-300 shadow-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm p-2.5" placeholder="Contoh: Cuci + Setrika Reguler" required>
                            @error('name') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Tipe Layanan</label>
                                <select wire:model="type" class="w-full rounded-lg border border-gray-300 shadow-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm p-2.5 bg-white">
                                    <option value="kiloan">Kiloan</option>
                                    <option value="satuan">Satuan</option>
                                    <option value="express">Express</option>
                                    <option value="sepatu">Sepatu</option>
                                </select>
                                @error('type') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Satuan</label>
                                <select wire:model="unit" class="w-full rounded-lg border border-gray-300 shadow-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm p-2.5 bg-white">
                                    <option value="kg">KG</option>
                                    <option value="pcs">Pcs</option>
                                    <option value="meter">Meter</option>
                                    <option value="pasang">Pasang</option>
                                </select>
                                @error('unit') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Harga per Satuan (Rp) <span class="text-rose-500">*</span></label>
                            <input wire:model="price" type="number" min="0" class="w-full rounded-lg border border-gray-300 shadow-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm p-2.5" placeholder="Contoh: 8000" required>
                            @error('price') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex items-center pt-1">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input wire:model="is_active" type="checkbox" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2.5 text-sm font-semibold text-gray-800">Status Layanan Aktif (Tersedia untuk order)</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                    <button type="submit" wire:loading.attr="disabled" class="inline-flex justify-center items-center px-4 py-2.5 bg-blue-600 border border-transparent rounded-xl font-semibold text-sm text-white shadow-sm hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-60 transition-colors">
                        <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span wire:loading.remove wire:target="save">Simpan Layanan</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </button>
                    <button type="button" wire:click="closeModal" class="inline-flex justify-center items-center px-4 py-2.5 bg-white border border-gray-300 rounded-xl font-semibold text-sm text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-none transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
