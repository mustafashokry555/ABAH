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
    public $timestamps = false;
    protected $fillable = [
        'NationalNo',
        'First_Name',
        'Middle_Name',
        'Last_Name',
        'Mobile',
        'Registration_No',
        'PatientSubType_ID',
        'InsertedByUserID',
        'Gender',
        'Nationality',
        'PatientType_ID',
        'patient_password',
    ];
    protected $hidden = [
        'patient_password',
    ];
}
