<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['category_id', 'sku', 'name', 'price', 'cost', 'stock', 'min_stock', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeLowStock(Builder $q): Builder
    {
        return $q->whereColumn('stock', '<=', 'min_stock');
    }

    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        return $term
            ? $q->where(fn ($w) => $w->where('name', 'like', "%{$term}%")->orWhere('sku', 'like', "%{$term}%"))
            : $q;
    }
}
