<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    protected $table = 'students';

    protected $fillable = [
        'name',
        'last_name',
        'age',
        'gender',
        'address',
        'email',
        'phone',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    use SoftDeletes;
}