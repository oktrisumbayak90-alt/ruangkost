<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'pengguna';

    protected $fillable = [
        'nama',
        'username',
        'email',
        'password',
        'peran',
        'telepon',
        'nomor_identitas',
        'jenis_kelamin',
        'alamat',
        'foto',
        'is_aktif',
        'wajib_ganti_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'             => 'hashed',
            'is_aktif'             => 'boolean',
            'wajib_ganti_password' => 'boolean',
        ];
    }

    // Role helpers
    public function isOwner(): bool
    {
        return in_array($this->peran, ['pemilik', 'owner']);
    }

    public function isTenant(): bool
    {
        return in_array($this->peran, ['penghuni', 'tenant']);
    }

    // Accessors & Mutators for backward compatibility
    public function getNameAttribute() { return $this->attributes['nama'] ?? null; }
    public function setNameAttribute($value) { $this->attributes['nama'] = $value; }

    public function getPhoneAttribute() { return $this->attributes['telepon'] ?? null; }
    public function setPhoneAttribute($value) { $this->attributes['telepon'] = $value; }

    public function getAddressAttribute() { return $this->attributes['alamat'] ?? null; }
    public function setAddressAttribute($value) { $this->attributes['alamat'] = $value; }

    public function getGenderAttribute() { return $this->attributes['jenis_kelamin'] ?? null; }
    public function setGenderAttribute($value) { $this->attributes['jenis_kelamin'] = $value; }

    public function getRoleAttribute() { return $this->attributes['peran'] ?? null; }
    public function setRoleAttribute($value) { $this->attributes['peran'] = $value; }

    public function getIdNumberAttribute() { return $this->attributes['nomor_identitas'] ?? null; }
    public function setIdNumberAttribute($value) { $this->attributes['nomor_identitas'] = $value; }

    public function getPhotoAttribute() { return $this->attributes['foto'] ?? null; }
    public function setPhotoAttribute($value) { $this->attributes['foto'] = $value; }

    public function getIsActiveAttribute() { return (bool) ($this->attributes['is_aktif'] ?? true); }
    public function setIsActiveAttribute($value) { $this->attributes['is_aktif'] = (bool) $value; }

    public function getMustChangePasswordAttribute() { return (bool) ($this->attributes['wajib_ganti_password'] ?? false); }
    public function setMustChangePasswordAttribute($value) { $this->attributes['wajib_ganti_password'] = (bool) $value; }

    public function getKataSandiAttribute() { return $this->attributes['password'] ?? null; }
    public function setKataSandiAttribute($value) { $this->attributes['password'] = $value; }

    // Relationships
    public function tenantStays()
    {
        return $this->hasMany(TenantStay::class, 'pengguna_id');
    }

    public function activeStay()
    {
        return $this->hasOne(TenantStay::class, 'pengguna_id')->where('status', 'aktif')->latest('id');
    }

    public function activeRoom()
    {
        return $this->hasOneThrough(
            Room::class,
            TenantStay::class,
            'pengguna_id',
            'id',
            'id',
            'kamar_id'
        )->where('riwayat_tinggal.status', 'aktif');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'pengguna_id');
    }

    public function reportResponses()
    {
        return $this->hasMany(ReportResponse::class, 'pengguna_id');
    }

    public function reportHistories()
    {
        return $this->hasMany(ReportHistory::class, 'pengguna_id');
    }

    public function notifications()
    {
        return $this->hasMany(AppNotification::class, 'pengguna_id');
    }

    public function unreadNotifications()
    {
        return $this->hasMany(AppNotification::class, 'pengguna_id')->where('is_dibaca', false);
    }
}
