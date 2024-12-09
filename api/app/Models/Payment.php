<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'plan_price',
        'discount',
        'amount_charged',
        'credit_remaining',
        'due_date',
        'status',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
