<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = ['name', 'company_name', 'phone', 'address', 'status', 'plan', 'plan_expires_at'];

    protected $casts = [
        'plan_expires_at' => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isPro(): bool
    {
        return strtolower($this->plan ?? 'free') === 'pro';
    }

    public function isFree(): bool
    {
        return !$this->isPro();
    }

    public function getOrderLimit(): ?int
    {
        return $this->isPro() ? null : 30;
    }

    public function getMonthlyOrdersCount(): int
    {
        return $this->orders()
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
    }

    public function getRemainingOrders(): ?int
    {
        if ($this->isPro()) {
            return null; // Unlimited
        }

        $limit = $this->getOrderLimit();
        $used = $this->getMonthlyOrdersCount();
        return max(0, $limit - $used);
    }

    public function canCreateOrder(): bool
    {
        if ($this->isPro()) {
            return true;
        }

        return $this->getMonthlyOrdersCount() < $this->getOrderLimit();
    }

    public function canExportReport(): bool
    {
        return $this->isPro();
    }

    public function canSendWhatsApp(): bool
    {
        return $this->isPro();
    }

    public function hasWatermark(): bool
    {
        return $this->isFree();
    }
}
