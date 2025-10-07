<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class Sale extends Model
{
    protected $fillable = ['customer_id','quantity','price','status', 'project_id'];
    protected $dates = ['archived_at'];

     // --- Scopes ---
    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    // --- Helpers ---
    public function archive(): void
    {
        $this->archived_at = now();
        $this->save();
    }

    public function restore(): void
    {
        $this->archived_at = null;
        $this->save();
    }
    // ... relations ...

    public function customer()
    { 
        return $this->belongsTo(Customer::class, 'customer_id'); 
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    public function scopeShouldBeArchived($query)
    {
        return $query
            ->whereIn('status', ['deal', 'no_deal'])
            ->where('updated_at', '<=', now()->subMonths(2))
            ->whereNull('archived_at');
    }


    public function chats()
    {
        return $this->hasMany(FollowUpChat::class);
    }

    protected static function booted()
    {
        static::updated(function ($sale) {
            if ($sale->isDirty('status')) {
                $sale->followUps()->latest()->first()?->update([
                    'status' => $sale->status,
                ]);
            }
        });
    }
    

}
