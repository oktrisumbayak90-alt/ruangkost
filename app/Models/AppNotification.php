<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';

    protected $fillable = [
        'pengguna_id',
        'tipe',
        'judul',
        'pesan',
        'url',
        'laporan_id',
        'is_dibaca',
        'dibaca_pada',
    ];

    protected function casts(): array
    {
        return [
            'is_dibaca'   => 'boolean',
            'dibaca_pada' => 'datetime',
        ];
    }

    // Accessors & Mutators for backward compatibility
    public function getUserIdAttribute() { return $this->attributes['pengguna_id'] ?? null; }
    public function setUserIdAttribute($value) { $this->attributes['pengguna_id'] = $value; }

    public function getTypeAttribute() { return $this->attributes['tipe'] ?? null; }
    public function setTypeAttribute($value) { $this->attributes['tipe'] = $value; }

    public function getTitleAttribute() { return $this->attributes['judul'] ?? null; }
    public function setTitleAttribute($value) { $this->attributes['judul'] = $value; }

    public function getMessageAttribute() { return $this->attributes['pesan'] ?? null; }
    public function setMessageAttribute($value) { $this->attributes['pesan'] = $value; }

    public function getReportIdAttribute() { return $this->attributes['laporan_id'] ?? null; }
    public function setReportIdAttribute($value) { $this->attributes['laporan_id'] = $value; }

    public function getIsReadAttribute() { return (bool) ($this->attributes['is_dibaca'] ?? false); }
    public function setIsReadAttribute($value) { $this->attributes['is_dibaca'] = (bool) $value; }

    public function getReadAtAttribute() { return $this->attributes['dibaca_pada'] ?? null; }
    public function setReadAtAttribute($value) { $this->attributes['dibaca_pada'] = $value; }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    public function report()
    {
        return $this->belongsTo(Report::class, 'laporan_id');
    }

    // Helper
    public function markAsRead(): void
    {
        $this->update([
            'is_dibaca'   => true,
            'dibaca_pada' => now(),
        ]);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_dibaca', false);
    }
}
