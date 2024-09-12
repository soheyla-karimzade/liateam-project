<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model as Eloquent;


class Product extends Eloquent
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $fillable = ['name', 'price', 'inventory'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
