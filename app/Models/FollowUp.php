<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
     protected $fillable = ['sale_id','customer_id','follow_up_date','result', 'status',];
   
    protected $casts = [
        'follow_up_date' => 'date', // ⬅️ biar otomatis jadi Carbon
    ];
   
   
     public function customer()
    { 
        return $this->belongsTo(Customer::class,  'customer_id'); 
    }

    public function sale()
{
    return $this->belongsTo(Sale::class, 'sale_id');
}

// otomatis isi customer_id kalau belum ada
    protected static function booted()
    {
        static::creating(function ($followUp) {
            if ($followUp->sale && empty($followUp->customer_id)) {
                $followUp->customer_id = $followUp->sale->customer_id;
            }
        });

          static::saved(function ($followUp) {
            if ($followUp->sale_id) {
                // Cari follow up terakhir untuk sale ini
                $latest = $followUp->sale->followUps()
                    ->latest('follow_up_date')
                    ->first();

                if ($latest && $latest->status) {
                    $followUp->sale->update([
                        'status' => $latest->status,
                    ]);
                }
            }
        });

        // Saat menghapus follow up → update status deals ke follow up terakhir
        static::deleted(function ($followUp) {
            if ($followUp->sale_id) {
                $latest = $followUp->sale->followUps()
                    ->latest('follow_up_date')
                    ->first();

                $followUp->sale->update([
                    'status' => $latest?->status ?? 'prospect',
                ]);
            }
        });
    }

    // App\Models\FollowUp.php
    public function chats()
    {
        return $this->hasMany(FollowUpChat::class);
    }


}
