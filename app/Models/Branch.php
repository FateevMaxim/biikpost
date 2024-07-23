<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'china_address',
        'whats_app'
    ];

    protected $hidden =
        [
            'created_at',
            'updated_at'
        ];

    public function users()
    {
        return $this->hasMany(User::class, 'branch', 'title');
    }
}
