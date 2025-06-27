<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'preferences',
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
            'preferences' => 'array',
            'password' => 'hashed',
        ];
    }

    /**
     * Get columns configuration for users index page.
     */
    public function getPageColumns(): array
    {
        return [
            [
                'key' => 'id',
                'label' => 'ID',
                'width' => 1,
            ],
            [
                'key' => 'avatar',
                'label' => 'Avatar',
                'unsortable' => true,
                'width' => 1,
            ],
            [
                'key' => 'name',
                'label' => 'Naam',
                'visible' => true,
                'default' => 'asc'
            ],
            [
                'key' => 'email',
                'label' => 'E-mailadres',
                'visible' => true
            ],
            [
                'key' => 'email_verified_at',
                'label' => 'E-mailadres geverifieerd op'
            ],
            [
                'key' => 'created_at',
                'label' => 'Aangemaakt op',
                'visible' => true
            ],
            [
                'key' => 'updated_at',
                'label' => 'Aangepast op'
            ],
        ];
    }
}
