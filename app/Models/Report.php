<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'laporan';

    protected $fillable = [
        'nomor_laporan',
        'pengguna_id',
        'kategori_id',
        'kamar_id',
        'riwayat_tinggal_id',
        'judul',
        'deskripsi',
        'lokasi',
        'tanggal_kejadian',
        'prioritas',
        'status',
        'jalur_foto',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kejadian' => 'date',
        ];
    }

    // Accessors & Mutators for backward compatibility
    public function getReportNumberAttribute() { return $this->attributes['nomor_laporan'] ?? null; }
    public function setReportNumberAttribute($value) { $this->attributes['nomor_laporan'] = $value; }

    public function getUserIdAttribute() { return $this->attributes['pengguna_id'] ?? null; }
    public function setUserIdAttribute($value) { $this->attributes['pengguna_id'] = $value; }

    public function getCategoryIdAttribute() { return $this->attributes['kategori_id'] ?? null; }
    public function setCategoryIdAttribute($value) { $this->attributes['kategori_id'] = $value; }

    public function getRoomIdAttribute() { return $this->attributes['kamar_id'] ?? null; }
    public function setRoomIdAttribute($value) { $this->attributes['kamar_id'] = $value; }

    public function getTenantStayIdAttribute() { return $this->attributes['riwayat_tinggal_id'] ?? null; }
    public function setTenantStayIdAttribute($value) { $this->attributes['riwayat_tinggal_id'] = $value; }

    public function getTitleAttribute() { return $this->attributes['judul'] ?? null; }
    public function setTitleAttribute($value) { $this->attributes['judul'] = $value; }

    public function getDescriptionAttribute() { return $this->attributes['deskripsi'] ?? null; }
    public function setDescriptionAttribute($value) { $this->attributes['deskripsi'] = $value; }

    public function getLocationAttribute() { return $this->attributes['lokasi'] ?? null; }
    public function setLocationAttribute($value) { $this->attributes['lokasi'] = $value; }

    public function getIncidentDateAttribute() { return $this->attributes['tanggal_kejadian'] ?? null; }
    public function setIncidentDateAttribute($value) { $this->attributes['tanggal_kejadian'] = $value; }

    public function getPriorityAttribute()
    {
        $val = $this->attributes['prioritas'] ?? null;
        return match ($val) {
            'tinggi', 'high'   => 'high',
            'sedang', 'medium' => 'medium',
            'rendah', 'low'    => 'low',
            default            => $val,
        };
    }

    public function setPrioritasAttribute($value)
    {
        $map = [
            'low'    => 'rendah',
            'medium' => 'sedang',
            'high'   => 'tinggi',
            'rendah' => 'rendah',
            'sedang' => 'sedang',
            'tinggi' => 'tinggi',
        ];
        $this->attributes['prioritas'] = $map[strtolower((string)$value)] ?? 'sedang';
    }

    public function setPriorityAttribute($value)
    {
        $this->setPrioritasAttribute($value);
    }

    public function getStatusAttribute()
    {
        $val = $this->attributes['status'] ?? null;
        return match ($val) {
            'menunggu'   => 'pending',
            'diproses'   => 'in_progress',
            'selesai'    => 'completed',
            'dibatalkan' => 'cancelled',
            default      => $val,
        };
    }

    public function setStatusAttribute($value)
    {
        $map = [
            'pending'     => 'menunggu',
            'in_progress' => 'diproses',
            'completed'   => 'selesai',
            'cancelled'   => 'dibatalkan',
            'menunggu'    => 'menunggu',
            'diproses'    => 'diproses',
            'selesai'     => 'selesai',
            'dibatalkan'  => 'dibatalkan',
        ];
        $this->attributes['status'] = $map[strtolower((string)$value)] ?? 'menunggu';
    }

    public function getImagePathAttribute() { return $this->attributes['jalur_foto'] ?? null; }
    public function setImagePathAttribute($value) { $this->attributes['jalur_foto'] = $value; }

    // Boot - generate nomor_laporan
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($report) {
            if (empty($report->nomor_laporan)) {
                $report->nomor_laporan = static::generateReportNumber();
            }
        });
    }

    public static function generateReportNumber(): string
    {
        $prefix = 'LP';
        $date = now()->format('Ymd');
        $prefixDate = $prefix . '-' . $date . '-';

        $lastReport = static::withTrashed()
            ->where('nomor_laporan', 'like', $prefixDate . '%')
            ->orderByDesc('nomor_laporan')
            ->first();

        $nextNumber = 1;
        if ($lastReport) {
            $lastSeq = (int) substr($lastReport->nomor_laporan, strlen($prefixDate));
            $nextNumber = $lastSeq + 1;
        }

        return $prefixDate . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function user()
    {
        // withTrashed() agar laporan penghuni yang dihapus tetap terbaca
        return $this->belongsTo(User::class, 'pengguna_id')->withTrashed();
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'kamar_id');
    }

    public function tenantStay()
    {
        return $this->belongsTo(TenantStay::class, 'riwayat_tinggal_id');
    }

    public function responses()
    {
        return $this->hasMany(ReportResponse::class, 'laporan_id')->orderBy('created_at');
    }

    public function histories()
    {
        return $this->hasMany(ReportHistory::class, 'laporan_id')->orderBy('created_at');
    }

    public function notifications()
    {
        return $this->hasMany(AppNotification::class, 'laporan_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereIn('status', ['menunggu', 'pending']);
    }

    public function scopeInProgress($query)
    {
        return $query->whereIn('status', ['diproses', 'in_progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['selesai', 'completed']);
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('prioritas', ['tinggi', 'high']);
    }

    // Label helpers
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu', 'pending'       => 'Menunggu',
            'diproses', 'in_progress'   => 'Diproses',
            'selesai', 'completed'      => 'Selesai',
            'dibatalkan', 'cancelled'   => 'Dibatalkan',
            default                     => 'Tidak Diketahui',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'menunggu', 'pending'       => 'warning',
            'diproses', 'in_progress'   => 'info',
            'selesai', 'completed'      => 'success',
            'dibatalkan', 'cancelled'   => 'secondary',
            default                     => 'light',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->prioritas) {
            'rendah', 'low'     => 'Rendah',
            'sedang', 'medium'  => 'Sedang',
            'tinggi', 'high'    => 'Tinggi',
            default             => 'Tidak Diketahui',
        };
    }

    public function getPriorityBadgeAttribute(): string
    {
        return match ($this->prioritas) {
            'rendah', 'low'     => 'success',
            'sedang', 'medium'  => 'warning',
            'tinggi', 'high'    => 'danger',
            default             => 'light',
        };
    }

    public function isEditable(): bool
    {
        return in_array($this->status, ['menunggu', 'pending']);
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['menunggu', 'pending']);
    }
}
