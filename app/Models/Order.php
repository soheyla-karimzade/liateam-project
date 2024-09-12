<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model as Eloquent;

class Order extends Eloquent
{
    use HasFactory;

    protected $connection = 'mongodb';

    protected $fillable = ['products', 'count', 'total_price'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
