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
            IF OBJECT_ID('usp_app_api_GetAllVisitList', 'P') IS NOT NULL
            BEGIN
                DROP PROCEDURE usp_app_api_GetAllVisitList;
            END
        ");

        DB::unprepared("
            create proc [dbo].[usp_app_api_GetAllVisitList]   
            @PatientId as Int,
            @UnitNo as Int
            AS
            Declare @Count as Int
            BEGIN
                Select @Count=count(*) from Visit V inner join Patient P on V.PatientID=P.PatientId
                inner join Department_Mst DM on V.DepartmentID=DM.Department_ID
                Where P.PatientId=@PatientId and V.LocationID=@UnitNo

                If @Count>0
                Begin
                    Select Visit_ID,VisitNo,convert(varchar,VisitDate,105) as VisitDate,SUBSTRING(CONVERT(varchar,VisitDate,108),1,5) AS VisitTime,dbo.fn_DoctorFullName(V.DocInCharge) as DoctorName,Department_Name as DepartmentName,1 as Id 
                    from Visit V inner join Patient P on V.PatientID=P.PatientId
                    inner join Department_Mst DM on V.DepartmentID=DM.Department_ID
                    Where P.PatientId=@PatientId and V.LocationID=@UnitNo
                    order by V.VisitDate desc
                End
                Else
                Begin
                    Select -1 as Id,'No Visit List Available' as Msg
                End
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
