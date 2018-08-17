<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegisterVerify extends Model
{
    protected $table = 'register_verify';

    protected $fillable = [
        'email', 'token'
    ];
}
