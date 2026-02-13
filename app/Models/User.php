<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function usesProfessionalEmail(): bool
    {
        $publicDomains = [
            'gmail',
            'googlemail',
            'yahoo',
            'ymail',
            'rocketmail',
            'hotmail',
            'live',
            'outlook',
            'msn',
            'windowslive',
            'sfr',
            'neuf',
            'club-internet',
            'cegetel',
            'free',
            'aliceadsl',
            'orange',
            'wanadoo',
            'voila',
            'laposte',
            'bbox',
            'numericable',
            'noos',
            'aol',
            'aim',
            'icloud',
            'me',
            'mac',
            'protonmail',
            'proton',
            'gmx',
            'yandex',
            'mail',
            'zoho'
        ];

        $pattern = '/@(' . implode('|', $publicDomains) . ')\./i';
        return !preg_match($pattern, $this->email);
    }
}
