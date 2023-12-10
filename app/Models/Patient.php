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
    const CREATED_AT = 'InsertedON';
    const UPDATED_AT = 'UpdatedOn';
    protected $fillable = [
        'NationalNo',
        'First_Name',
        'Middle_Name',
        'Last_Name',
        'Mobile',
        'Registration_No',
        'PatientSubType_ID',
        'Gender',
        'Nationality',
        'PatientType_ID',
        'patient_password',
        
        //defult
        'InsertedByUserID',
        'InsertedMacName',
        'InsertedMacID',
        'InsertedIPAddress',
        'Hospital_ID',
        'Deactive',
    ];
    protected $hidden = [
        'patient_password',
    ];
}
