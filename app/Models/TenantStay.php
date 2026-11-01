<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantStay extends Model
{
    use HasFactory;

    protected $table = 'riwayat_tinggal';

    protected $fillable = [
        'pengguna_id',
        'kamar_id',
        'tanggal_masuk',
        'tanggal_keluar',
        'jumlah_penghuni',
        'status',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_masuk'   => 'date',
            'tanggal_keluar'  => 'date',
            'jumlah_penghuni' => 'integer',
        ];
    }

    // Accessor for jumlah_penghuni (backward compatibility)
    public function getOccupantCountAttribute() { return $this->attributes['jumlah_penghuni'] ?? 1; }
    public function setOccupantCountAttribute($value) { $this->attributes['jumlah_penghuni'] = $value; }

    // Accessors & Mutators for backward compatibility
    public function getUserIdAttribute() { return $this->attributes['pengguna_id'] ?? null; }
    public function setUserIdAttribute($value) { $this->attributes['pengguna_id'] = $value; }

    public function getRoomIdAttribute() { return $this->attributes['kamar_id'] ?? null; }
    public function setRoomIdAttribute($value) { $this->attributes['kamar_id'] = $value; }

    public function getMoveInDateAttribute() { return $this->attributes['tanggal_masuk'] ?? null; }
    public function setMoveInDateAttribute($value) { $this->attributes['tanggal_masuk'] = $value; }

    public function getMoveOutDateAttribute() { return $this->attributes['tanggal_keluar'] ?? null; }
    public function setMoveOutDateAttribute($value) { $this->attributes['tanggal_keluar'] = $value; }

    public function getNotesAttribute() { return $this->attributes['catatan'] ?? null; }
    public function setNotesAttribute($value) { $this->attributes['catatan'] = $value; }

    // Status & Durasi Helper Accessors
    public function getIsActiveAttribute(): bool
    {
        return in_array($this->status, ['aktif', 'active']);
    }

    public function getDurationDaysAttribute(): int
    {
        if (!$this->move_in_date) {
            return 0;
        }

        $start = \Carbon\Carbon::parse($this->move_in_date)->startOfDay();
        $end = $this->move_out_date
            ? \Carbon\Carbon::parse($this->move_out_date)->startOfDay()
            : now()->startOfDay();

        if ($start->greaterThan($end)) {
            return 0;
        }

        return (int) round($start->diffInDays($end));
    }

    public function getFormattedDurationAttribute(): string
    {
        if (!$this->move_in_date) {
            return '-';
        }

        $start = \Carbon\Carbon::parse($this->move_in_date)->startOfDay();
        $end = $this->move_out_date
            ? \Carbon\Carbon::parse($this->move_out_date)->startOfDay()
            : now()->startOfDay();

        if ($start->greaterThan($end)) {
            return 'Belum mulai';
        }

        $totalDays = (int) round($start->diffInDays($end));

        if ($totalDays === 0) {
            return 'Hari pertama (1 hari)';
        }

        $diff = $start->diff($end);
        $parts = [];
        if ($diff->y > 0) {
            $parts[] = "{$diff->y} tahun";
        }
        if ($diff->m > 0) {
            $parts[] = "{$diff->m} bulan";
        }
        if ($diff->d > 0 && $diff->y === 0) {
            $parts[] = "{$diff->d} hari";
        }

        $human = !empty($parts) ? implode(' ', $parts) : "{$totalDays} hari";

        return $totalDays < 30 ? "{$totalDays} hari" : "{$human} ({$totalDays} hari)";
    }

    // Relationships
    public function user()
    {
        // withTrashed() agar penghuni yang dihapus (soft delete) tetap terbaca
        return $this->belongsTo(User::class, 'pengguna_id')->withTrashed();
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'kamar_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'riwayat_tinggal_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['aktif', 'active']);
    }

    public function scopeEnded($query)
    {
        return $query->whereIn('status', ['selesai', 'ended']);
    }
}
