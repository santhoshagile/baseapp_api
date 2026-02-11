<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Institutions extends Model
{
    use HasFactory, HasSlug;

    public $table = 'institutions';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'name'
        , 'type'
        , 'address'
        , 'status'
        , 'slug'
        , 'created_by'
        , 'updated_by',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

}
