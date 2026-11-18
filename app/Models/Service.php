<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'icon_class',
        'short_description',
        'full_description',
        'features',
        'images',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'images' => 'array',
        'is_active' => 'boolean',
    ];
}
