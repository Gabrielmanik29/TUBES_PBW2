<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'stock', 'category_id'];

    // Default values
    protected $attributes = [
        'stock' => 0,
    ];

    // Relations
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }

    // Accessor untuk stok tersedia
    public function getStockTersediaAttribute()
    {
        try {
            $dipinjam = $this->peminjamans()
                ->whereIn('status', ['diajukan', 'disetujui'])
                ->sum('quantity');
            
            return max(0, $this->stock - $dipinjam);
        } catch (\Exception $e) {
            // Jika ada error, return stock biasa
            return $this->stock;
        }
    }

    // Scope untuk barang yang tersedia
    public function scopeAvailable($query)
    {
        return $query->where('stock', '>', 0);
    }

    // Scope untuk barang dengan kategori tertentu
    public function scopeByCategory($query, $categoryId)
    {
        if ($categoryId) {
            return $query->where('category_id', $categoryId);
        }
        return $query;
    }

    // Scope untuk search
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
        }
        return $query;
    }
}