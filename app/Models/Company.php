<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Team;
use App\Models\Role;
use App\Models\User;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'logo',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function role()
    {
        return $this->hasMany(Role::class);
    }
}
