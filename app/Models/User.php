<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, \App\Traits\NormalizePhoneNumber;

    protected $fillable = [
        'name', 'username', 'password', 'role',
        'nomor_hp', 'alamat', 'foto_profil', 'kategori_member', 'membership_expires_at',
        // Loyalty Points
        'poin_saldo', 'poin_bulanan', 'segmen_pelanggan', 'segmen_updated_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->username)) {
                // Generate from name, otherwise unique random
                $base = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', $user->name));
                
                if (empty($base)) {
                    $base = 'user';
                }

                $username = $base;
                $counter = 1;
                while (static::where('username', $username)->exists()) {
                    $username = $base . $counter;
                    $counter++;
                }
                $user->username = $username;
            }
        });

        static::updating(function ($user) {
            if ($user->isDirty('name')) {
                $oldName = $user->getOriginal('name');
                $newName = $user->name;
                if (!empty($oldName)) {
                    \App\Models\Jadwal::where('tanggal', '>=', now()->toDateString())
                        ->whereIn('keterangan', [
                            'Slot Member: ' . $oldName,
                            'Slot Member: ' . $oldName . ' (#' . $user->id . ')'
                        ])
                        ->update(['keterangan' => 'Slot Member: ' . $newName . ' (#' . $user->id . ')']);
                }
            }
        });
    }

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password'              => 'hashed',
            'segmen_updated_at'     => 'datetime',
            'membership_expires_at' => 'datetime',
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

    /**
     * Mutator untuk menormalisasi format nomor HP sebelum disimpan.
     */
    public function setNomorHpAttribute($value)
    {
        $this->attributes['nomor_hp'] = $this->normalizePhoneNumber($value);
    }

    /**
     * Hitung sisa hari aktif member.
     * Mengembalikan 0 jika sudah kadaluwarsa atau bukan member.
     */
    public function sisaHariAktifMember(): int
    {
        if (!$this->isMember() || !$this->membership_expires_at) {
            return 0;
        }

        if ($this->membership_expires_at->isPast()) {
            return 0;
        }

        return (int) ceil(now()->diffInHours($this->membership_expires_at, false) / 24);
    }
}
