<?php

namespace App\Models;

use App\Traits\QueryFilterable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, QueryFilterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $visible = [
        'id', 'name', 'email',
    ];

    /**
     * The fields that should be filterable by query.
     *
     * @var array
     */
    protected $filterable = [
        'name', 'email',
    ];

    /**
     * Hash the password attribute.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute(string $value = null): void
    {
        if (!empty($value)) {
            $this->attributes['password'] = app('hash')->make($value);
        }
    }
}
