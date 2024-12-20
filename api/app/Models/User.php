<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $hidden = ['created_at', 'updated_at'];

    public function contract()
    {
        return $this->hasMany(Contract::class);
    }
}
