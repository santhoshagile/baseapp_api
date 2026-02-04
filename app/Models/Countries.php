<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\SlugOptions;

class Countries extends Model
{
    use HasFactory;

    public $table = 'countries';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'name'
        , 'mobile_code'
        , 'header_id'
        , 'lang'
        , 'status'
        , 'slug'
        , 'created_by'
        , 'updated_by',
    ];

    public function states()
    {
        return $this->hasMany('App\Models\States', 'country_id', 'id');
    }

}
