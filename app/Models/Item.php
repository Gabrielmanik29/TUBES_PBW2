<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'stock', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function getStockTersediaAttribute()
    {
        $dipinjam = $this->peminjamans()
            ->whereIn('status', ['diajukan', 'disetujui'])
            ->sum('quantity');
        
        return $this->stock - $dipinjam;
    }
}
