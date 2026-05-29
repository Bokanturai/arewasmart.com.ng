<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Basic info
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'phone_no',
        'address',
        'photo',
        'pin',
        'profile_photo_url',

        // Identity
        'bvn',
        'nin',
        'tin',
        'state',
        'lga',
        'nearest_bus_stop',
        'business_name',

        // Referral system
        'referral_code',
        'referral_bonus',
        'referred_by',
        'performed_by',
        'approved_by',

        // Other
        'claim_id',
        'role',
        'password',
        'limit',
        'can_apply_loan',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'pin',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'can_apply_loan'    => 'boolean',
        ];
    }

    /**
     * Get or set the user's BVN securely and with backward-compatibility.
     */
    protected function bvn(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                if (empty($value)) return $value;
                try {
                    return decrypt($value);
                } catch (\Exception $e) {
                    // Fallback to raw value if it is not encrypted (backward compatibility)
                    return $value;
                }
            },
            set: function (?string $value) {
                if (empty($value)) return $value;
                return encrypt($value);
            }
        );
    }

    /**
     * Get or set the user's NIN securely and with backward-compatibility.
     */
    protected function nin(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                if (empty($value)) return $value;
                try {
                    return decrypt($value);
                } catch (\Exception $e) {
                    // Fallback to raw value if it is not encrypted (backward compatibility)
                    return $value;
                }
            },
            set: function (?string $value) {
                if (empty($value)) return $value;
                return encrypt($value);
            }
        );
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
}

