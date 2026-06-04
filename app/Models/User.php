<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'nomor_hp', 'alamat', 'foto_profil', 'kategori_member',
        // Loyalty Points
        'poin_saldo', 'poin_bulanan', 'segmen_pelanggan', 'segmen_updated_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'password'           => 'hashed',
            'segmen_updated_at'  => 'datetime',
        ];
    }

    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isPelanggan(): bool { return $this->role === 'pelanggan'; }
    public function isMember(): bool   { return in_array($this->kategori_member, ['member', 'weekday_pagi', 'weekday_malam', 'weekend']); }

    public function bookings()      { return $this->hasMany(Booking::class); }
    public function pointsHistory() { return $this->hasMany(PointHistory::class); }
    public function redemptions()   { return $this->hasMany(Redemption::class); }
    public function vouchers()      { return $this->hasMany(Voucher::class); }

    // ─── Loyalty: Accessor & Helper ──────────────────────────────────

    /**
     * Label teks segmen untuk tampilan UI pelanggan & admin.
     */
    public function getLabelSegmenAttribute(): string
    {
        return match($this->segmen_pelanggan) {
            'visitor'  => '👤 Visitor',
            'ally'     => '🤝 Ally',
            'partner'  => '🏸 Partner',
            'loyalist' => '👑 Loyalist',
            'vip'      => '💎 VIP',
            default    => '—',
        };
    }

    /**
     * CSS class badge segmen untuk tampilan tabel admin.
     */
    public function getBadgeSegmenAttribute(): string
    {
        return match($this->segmen_pelanggan) {
            'visitor'  => 'badge-secondary',
            'ally'     => 'badge-info',
            'partner'  => 'badge-success',
            'loyalist' => 'badge-warning',
            'vip'      => 'badge-danger',
            default    => 'badge-secondary',
        };
    }
}
