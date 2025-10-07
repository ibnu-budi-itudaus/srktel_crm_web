<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name','phone','email','address','source_id','status'];
    public function source()
    { 
        return $this->belongsTo(Source::class, 'source_id'); 
    }
    public function sales()
    {
         return $this->hasMany(Sale::class); 
    }
    public function followUps()
    { 
        return $this->hasMany(FollowUp::class); 
    }
}
