<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'lastname',
        'email',
        'password',
        'token_id',
        'role_id',
        'salutation',
        'gender',
        'dob',
        'addressdob',
        'postcode',
        'description',
        'image_url',
        'country',
        'state',
        'city',
        'mobile',
        'mobile_code',
        'otp',
        'otp_valid_until',
        'is_otp_validated',
        'status',
        'slug',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['rolename', 'full_name'];

    public function getFullNameAttribute()
    {
 
        if ($this->name) {
            return $this->name . " " . $this->lastname;
        } else {
            return '';
        }
    }
 
    public function getRoleNameAttribute()
    {
        $roledata = Role::where('id', $this->role_id)->first();
        if ($roledata) {
            return $roledata->rolename;
        } else {
            return '';
        }
    }
   
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
    public function role()
    {
        return $this->belongsTo('App\Models\Role', 'role_id', 'id');
    }
    public function country()
    {
        return $this->belongsTo('App\Models\Countries', 'country', 'id');
    }
    public function state()
    {
        return $this->belongsTo('App\Models\States', 'state', 'id');
    }
    public function city()
    {
        return $this->belongsTo('App\Models\Cities', 'city', 'id');
    }
}
