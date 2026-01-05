<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

<<<<<<< HEAD
    protected $fillable = ['name', 'description', 'stock', 'category_id'];
=======
    protected $fillable = ['name', 'description', 'stock', 'category_id', 'photo'];
>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2

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

    public function isAvailableForBorrow($quantity)
    {
        $dipinjam = $this->peminjamans()
            ->whereIn('status', ['diajukan', 'disetujui'])
            ->sum('quantity');

        $stokTersedia = $this->stock - $dipinjam;

        return $stokTersedia >= $quantity;
    }
}
