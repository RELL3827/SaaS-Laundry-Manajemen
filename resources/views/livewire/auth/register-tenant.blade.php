<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

new #[Layout('components.layouts.app')] class extends Component
{
    #[Url]
    public $plan = 'free';

    public $business_name = '';
    public $name = '';
    public $email = '';
    public $password = '';

    public function mount()
    {
        $requestedPlan = request()->query('plan');
        if (in_array($requestedPlan, ['free', 'pro'])) {
            $this->plan = $requestedPlan;
        }
    }

    public function selectPlan($plan)
    {
        if (in_array($plan, ['free', 'pro'])) {
            $this->plan = $plan;
        }
    }

    public function register()
    {
        $this->validate([
            'business_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'plan' => 'required|in:free,pro',
        ]);

        DB::transaction(function () {
            $tenant = Tenant::create([
                'name' => $this->business_name,
                'plan' => $this->plan,
            ]);

            $role = Role::firstOrCreate(['name' => 'owner'], ['display_name' => 'Owner']);

            $user = User::create([
                'tenant_id' => $tenant->id,
                'role_id' => $role->id,
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            Auth::login($user);
        });

        return redirect()->intended('/dashboard');
    }
};
?>

<div class="min-h-[85vh] flex flex-col items-center justify-center p-4">
    <!-- Back to Home Link -->
    <div class="max-w-lg w-full mb-4">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Beranda Utama</span>
        </a>
    </div>

    <div class="max-w-lg w-full bg-white rounded-3xl shadow-xl border border-slate-200/80 p-6 sm:p-8">
        <div class="text-center mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mb-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <span class="text-2xl font-extrabold text-slate-900 tracking-tight">Laundry<span class="text-blue-600">Pro</span></span>
            </a>
            <h1 class="text-xl font-bold text-slate-900">Pendaftaran Akun Bisnis</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pilih paket langganan dan mulai kelola outlet laundry Anda</p>
        </div>

        <form wire:submit="register" class="space-y-4">
            <!-- Plan Selector -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Paket Langganan</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Starter / Free Card -->
                    <div wire:click="selectPlan('free')" class="cursor-pointer rounded-2xl p-4 border-2 transition-all relative {{ $plan === 'free' ? 'border-blue-600 bg-blue-50/50 shadow-sm' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                        @if($plan === 'free')
                            <div class="absolute top-3 right-3 w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            </div>
                        @endif
                        <span class="inline-block text-[11px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 mb-1">Starter</span>
                        <h4 class="font-extrabold text-slate-900 text-sm">Paket Gratis</h4>
                        <p class="text-lg font-black text-slate-900 mt-1">Rp 0</p>
                        <ul class="text-[11px] text-slate-600 mt-2 space-y-1">
                            <li class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Maks. 30 Order/bln</li>
                            <li class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Fitur POS Kasir Dasar</li>
                            <li class="flex items-center gap-1.5 text-slate-400"><svg class="w-3.5 h-3.5 text-slate-300 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg> Watermark di struk</li>
                        </ul>
                    </div>

                    <!-- Pro Card -->
                    <div wire:click="selectPlan('pro')" class="cursor-pointer rounded-2xl p-4 border-2 transition-all relative {{ $plan === 'pro' ? 'border-amber-500 bg-amber-50/40 shadow-md ring-2 ring-amber-400/30' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                        <div class="absolute -top-2.5 right-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-black text-[10px] uppercase px-2 py-0.5 rounded-full shadow-xs">
                            Populer ⭐
                        </div>
                        @if($plan === 'pro')
                            <div class="absolute top-4 right-3 w-5 h-5 rounded-full bg-amber-500 text-white flex items-center justify-center">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            </div>
                        @endif
                        <span class="inline-block text-[11px] font-extrabold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 mb-1">👑 Pro Member</span>
                        <h4 class="font-extrabold text-slate-900 text-sm">Paket Pro</h4>
                        <p class="text-lg font-black text-slate-900 mt-1">Rp 99.000 <span class="text-[11px] font-normal text-slate-500">/bln</span></p>
                        <ul class="text-[11px] text-slate-700 mt-2 space-y-1">
                            <li class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> <b>Unlimited Order</b></li>
                            <li class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> <b>Nota WhatsApp Otomatis</b></li>
                            <li class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Ekspor CSV & Struk Bersih</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Outlet / Usaha Laundry</label>
                <input wire:model="business_name" type="text" placeholder="Contoh: Berkah Laundry Express" class="w-full rounded-xl border border-slate-300 shadow-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm p-3 bg-white" required>
                @error('business_name') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap Pemilik (Owner)</label>
                <input wire:model="name" type="text" placeholder="Contoh: Budi Santoso" class="w-full rounded-xl border border-slate-300 shadow-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm p-3 bg-white" required>
                @error('name') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Email</label>
                <input wire:model="email" type="email" placeholder="owner@gmail.com" class="w-full rounded-xl border border-slate-300 shadow-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm p-3 bg-white" required>
                @error('email') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi (Minimal 8 Karakter)</label>
                <input wire:model="password" type="password" placeholder="••••••••" class="w-full rounded-xl border border-slate-300 shadow-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm p-3 bg-white" required minlength="8">
                @error('password') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-xl shadow-md text-sm font-extrabold text-white {{ $plan === 'pro' ? 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-blue-500/25' : 'bg-blue-600 hover:bg-blue-700' }} active:scale-95 transition-all flex items-center justify-center gap-2 mt-2">
                <svg wire:loading wire:target="register" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span>Daftar dengan {{ $plan === 'pro' ? 'Paket Pro (Aktifkan Semua Fitur)' : 'Paket Gratis' }}</span>
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-slate-100 text-center text-xs text-slate-500">
            <span>Sudah memiliki akun outlet?</span>
            <a href="/login" class="text-blue-600 font-bold hover:underline ml-1">Masuk ke Portal</a>
        </div>
    </div>
</div>