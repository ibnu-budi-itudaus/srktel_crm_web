<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUpChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'sender',
        'message',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
