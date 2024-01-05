<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared("
            IF OBJECT_ID('usp_app_api_DeleteAppointment', 'P') IS NOT NULL
            BEGIN
                DROP PROCEDURE usp_app_api_DeleteAppointment;
            END
        ");

        DB::unprepared("
            create proc [dbo].[usp_app_api_DeleteAppointment]   
            @TempId as Int,
            @PatientId as Int,
            @AppCode as NVARCHAR(50),
            @APPDate as NVARCHAR(50),
            @LocId as Int
            AS
            Declare @appstatus as Nvarchar(50),@Count as Int
            BEGIN
                select @Count=COUNT(*) from Ds_PatientAppoinmentTemperary where PatientId=@PatientId and AppointmentCode=@AppCode
                Select @appstatus=ApntStatus from Ds_PatientAppoinmentTemperary where PatientId=@PatientId and AppointmentCode=@AppCode

                --select * from Ds_PatientAppoinmentTemperary
                If @Count>0
                Begin
                If @appstatus like '%CANCEL%' --or @appstatus like '%CANCELED%'
                BEGIN
                    Update Ds_PatientAppoinmentTemperary set ApntStatus='CANCELED' where AppointmentCode=@AppCode and PatientId=@PatientId
                    Select 1 as Id
                END
                Else 
                BEGIN
                    IF(EXISTS( SELECT 1 AS Expr1 FROM  Ds_PatientAppoinment        
                    WHERE (ID = @tempId and app_date<=CONVERT(varchar,GETDATE(),23) )))
                    BEGIN        
                        DELETE FROM Ds_PatientAppoinment WHERE (ID = @tempId)        
                    END  	
                    Update Ds_PatientAppoinmentTemperary set ApntStatus ='CANCELED' WHERE (ID = @tempId) and app_date<=CONVERT(varchar,GETDATE(),23)
                    Select 1 as Id
                END
                END
                Else
                Begin
                    Select -1 as Id,
                    'Appointment cannot be Deleted,Please try again..' as Msg
                End
                --End
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
