<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;

class Customer extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'name', 'phone', 'email', 'address', 'points'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
