<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'path',
        'disk',
        'type',
        'order',
        'alt',
        'size',
        'mime_type',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'size' => 'integer',
        ];
    }

    /**
     * Get the parent imageable model (User, Student, Course, etc.).
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the full URL of the image.
     */
    public function getUrlAttribute(): string
    {
        if (!tenancy()->initialized) {
            return asset($this->path);
        }
        return tenant_asset($this->path) . '?tenant=' . tenant('id');
    }
}
