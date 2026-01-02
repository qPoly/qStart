<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organisation extends Model
{
    /** @use HasFactory<\Database\Factories\OrganisationFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'logo_path',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
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
                'key' => 'logo',
                'label' => 'Logo',
                'unsortable' => true,
                'width' => 1,
            ],
            [
                'key' => 'name',
                'label' => 'Naam',
                'visible' => true,
                'default' => 'asc',
            ],
            [
                'key' => 'created_at',
                'label' => 'Aangemaakt op',
                'visible' => true,
            ],
            [
                'key' => 'updated_at',
                'label' => 'Aangepast op',
            ],
        ];
    }
}
