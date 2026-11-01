<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportResponse extends Model
{
    use HasFactory;

    protected $table = 'tanggapan_laporan';

    protected $fillable = [
        'laporan_id',
        'pengguna_id',
        'tanggapan',
    ];

    // Accessors & Mutators for backward compatibility
    public function getReportIdAttribute() { return $this->attributes['laporan_id'] ?? null; }
    public function setReportIdAttribute($value) { $this->attributes['laporan_id'] = $value; }

    public function getUserIdAttribute() { return $this->attributes['pengguna_id'] ?? null; }
    public function setUserIdAttribute($value) { $this->attributes['pengguna_id'] = $value; }

    public function getResponseAttribute() { return $this->attributes['tanggapan'] ?? null; }
    public function setResponseAttribute($value) { $this->attributes['tanggapan'] = $value; }

    // Relationships
    public function report()
    {
        return $this->belongsTo(Report::class, 'laporan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }
}
