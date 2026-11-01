<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'ikon',
        'warna',
        'is_aktif',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'is_aktif' => 'boolean',
        ];
    }

    // Accessors & Mutators for backward compatibility
    public function getNameAttribute() { return $this->attributes['nama'] ?? null; }
    public function setNameAttribute($value) { $this->attributes['nama'] = $value; }

    public function getDescriptionAttribute() { return $this->attributes['deskripsi'] ?? null; }
    public function setDescriptionAttribute($value) { $this->attributes['deskripsi'] = $value; }

    public function getIconAttribute() { return $this->attributes['ikon'] ?? 'bi-tag'; }
    public function setIconAttribute($value) { $this->attributes['ikon'] = $value; }

    public function getColorAttribute() { return $this->attributes['warna'] ?? '#2563EB'; }
    public function setColorAttribute($value) { $this->attributes['warna'] = $value; }

    public function getIsActiveAttribute() { return (bool) ($this->attributes['is_aktif'] ?? true); }
    public function setIsActiveAttribute($value) { $this->attributes['is_aktif'] = (bool) $value; }

    public function getSortOrderAttribute() { return $this->attributes['urutan'] ?? 0; }
    public function setSortOrderAttribute($value) { $this->attributes['urutan'] = $value; }

    // Relationships
    public function reports()
    {
        return $this->hasMany(Report::class, 'kategori_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_aktif', true);
    }

    // Helper
    public function hasReports(): bool
    {
        return $this->reports()->exists();
    }
}
