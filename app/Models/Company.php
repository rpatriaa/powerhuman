<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'logo',
    ];

    public function user()
    {
        return $this->belongsToMany(User::class);
    }

    public function team()
    {
        return $this->hasMany(Team::class);
    }

    public function role()
    {
        return $this->hasMany(Role::class);
    }
}
