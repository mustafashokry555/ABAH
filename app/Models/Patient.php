<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Patient extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $table = 'patient';
    protected $primaryKey = 'Registration_No';

    protected $hidden = [
        'patient_password',
    ];
}
