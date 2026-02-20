<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'content',
        'url',
        'url_hash',
        'image_url',
        'published_at',
        'source_name',
        'author',
        'category',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function getShortDescriptionAttribute(): ?string
    {
        return $this->description ? substr($this->description, 0, 160) . '...' : null;
    }
}