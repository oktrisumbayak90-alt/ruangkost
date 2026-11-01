<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kamar';

    protected $fillable = [
        'nomor_kamar',
        'lantai',
        'kapasitas',
        'deskripsi',
        'status',
    ];

    // Accessors & Mutators for backward compatibility
    public function getRoomNumberAttribute() { return $this->attributes['nomor_kamar'] ?? null; }
    public function setRoomNumberAttribute($value) { $this->attributes['nomor_kamar'] = $value; }

    public function getFloorAttribute() { return $this->attributes['lantai'] ?? null; }
    public function setFloorAttribute($value) { $this->attributes['lantai'] = $value; }

    public function getCapacityAttribute() { return $this->attributes['kapasitas'] ?? 1; }
    public function setCapacityAttribute($value) { $this->attributes['kapasitas'] = $value; }

    public function getDescriptionAttribute() { return $this->attributes['deskripsi'] ?? null; }
    public function setDescriptionAttribute($value) { $this->attributes['deskripsi'] = $value; }

    // Relationships
    public function tenantStays()
    {
        return $this->hasMany(TenantStay::class, 'kamar_id');
    }

    public function activeStays()
    {
        return $this->hasMany(TenantStay::class, 'kamar_id')->whereIn('status', ['aktif', 'active']);
    }

    public function activeTenants()
    {
        return $this->hasManyThrough(
            User::class,
            TenantStay::class,
            'kamar_id',
            'id',
            'id',
            'pengguna_id'
        )->whereIn('riwayat_tinggal.status', ['aktif', 'active']);
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'kamar_id');
    }

    // Helper methods
    public function getActiveTenantsCount(): int
    {
        if ($this->relationLoaded('activeStays')) {
            return (int) $this->activeStays->sum(fn($s) => $s->jumlah_penghuni ?? 1);
        }
        $sum = $this->activeStays()->sum('jumlah_penghuni');
        return $sum > 0 ? (int) $sum : $this->activeStays()->count();
    }

    public function isAvailable(): bool
    {
        if (in_array($this->status, ['pemeliharaan', 'maintenance'])) {
            return false;
        }
        return $this->getActiveTenantsCount() < $this->kapasitas;
    }

    public function updateStatus(): void
    {
        if (in_array($this->status, ['pemeliharaan', 'maintenance'])) {
            if ($this->getActiveTenantsCount() === 0) {
                return;
            }
        }

        $activeCount = $this->getActiveTenantsCount();

        if ($activeCount === 0) {
            $this->status = 'tersedia';
        } elseif ($activeCount >= $this->kapasitas) {
            $this->status = $this->kapasitas === 1 ? 'terisi' : 'penuh';
        } else {
            $this->status = 'terisi';
        }

        $this->save();
    }

    // Status labels & badges
    public function getStatusLabelAttribute(): string
    {
        if (in_array($this->status, ['pemeliharaan', 'maintenance'])) {
            return 'Perbaikan';
        }

        $activeCount = $this->getActiveTenantsCount();
        if ($activeCount === 0) {
            return 'Kosong';
        }

        if ($activeCount >= $this->kapasitas) {
            return 'Terisi Penuh';
        }

        return 'Terisi';
    }

    public function getStatusBadgeAttribute(): string
    {
        if (in_array($this->status, ['pemeliharaan', 'maintenance'])) {
            return 'warning';
        }

        $activeCount = $this->getActiveTenantsCount();
        if ($activeCount === 0) {
            return 'success';
        }

        if ($activeCount >= $this->kapasitas) {
            return 'danger';
        }

        return 'primary';
    }
}
