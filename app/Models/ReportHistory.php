<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportHistory extends Model
{
    use HasFactory;

    protected $table = 'riwayat_laporan';

    protected $fillable = [
        'laporan_id',
        'pengguna_id',
        'status_lama',
        'status_baru',
        'catatan',
    ];

    // Accessors & Mutators for backward compatibility
    public function getReportIdAttribute() { return $this->attributes['laporan_id'] ?? null; }
    public function setReportIdAttribute($value) { $this->attributes['laporan_id'] = $value; }

    public function getUserIdAttribute() { return $this->attributes['pengguna_id'] ?? null; }
    public function setUserIdAttribute($value) { $this->attributes['pengguna_id'] = $value; }

    public function getOldStatusAttribute() { return $this->attributes['status_lama'] ?? null; }
    public function setOldStatusAttribute($value) { $this->attributes['status_lama'] = $value; }

    public function getNewStatusAttribute() { return $this->attributes['status_baru'] ?? null; }
    public function setNewStatusAttribute($value) { $this->attributes['status_baru'] = $value; }

    public function getNoteAttribute() { return $this->attributes['catatan'] ?? null; }
    public function setNoteAttribute($value) { $this->attributes['catatan'] = $value; }

    // Relationships
    public function report()
    {
        return $this->belongsTo(Report::class, 'laporan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    // Label helpers
    public function getOldStatusLabelAttribute(): string
    {
        return match ($this->status_lama) {
            'menunggu', 'pending'       => 'Menunggu',
            'diproses', 'in_progress'   => 'Diproses',
            'selesai', 'completed'      => 'Selesai',
            'dibatalkan', 'cancelled'   => 'Dibatalkan',
            null                        => '-',
            default                     => $this->status_lama,
        };
    }

    public function getNewStatusLabelAttribute(): string
    {
        return match ($this->status_baru) {
            'menunggu', 'pending'       => 'Menunggu',
            'diproses', 'in_progress'   => 'Diproses',
            'selesai', 'completed'      => 'Selesai',
            'dibatalkan', 'cancelled'   => 'Dibatalkan',
            default                     => $this->status_baru,
        };
    }
}
