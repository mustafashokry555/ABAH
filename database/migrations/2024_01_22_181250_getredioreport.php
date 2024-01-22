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
            IF OBJECT_ID('usp_app_apiGetAllRadioDatewise', 'P') IS NOT NULL
            BEGIN
                DROP PROCEDURE usp_app_apiGetAllRadioDatewise;
            END
        ");

        DB::unprepared("
            create proc [dbo].[usp_app_apiGetAllRadioDatewise]   
            @PatientId as Int,

            @LocId as Int

            AS
            Declare @Count as Int=0


            BEGIN

                Select @Count=Count(*) from OrderMst OM inner join 
                Visit V on V.Visit_ID=om.ordVisitId
                inner join OrderDtl Od on om.OrdId=Od.OrdID
                inner join Patient P on V.PatientID=P.PatientId 
                inner join IMGServices ig on ig.ServiceID=Od.ServiceId
            inner join hospital_mst HM on P.Hospital_ID=HM.Hospital_ID  where P.PatientId=@PatientId and HM.Hospital_ID=@LocId

            If @Count>0
            Begin
                select 1 as ID, OrdNo as OrderNo,convert(varchar,Om.OrdDateTime,106)  AS ReportDate, sm.Service_Name, sdm.SubDepartment_Name,Od.OrdDtlID
                from OrderMst OM inner join 
                Visit V on V.Visit_ID=om.ordVisitId
                inner join OrderDtl Od on om.OrdId=Od.OrdID
                inner join Service_mst sm on od.ServiceId= sm.Service_ID
                inner join SubDepartment_Mst sdm on sm.SubDepartmentId= sdm.SubDepartment_ID
                inner join Patient P on V.PatientID=P.PatientId 
                inner join IMGServices ig on ig.ServiceID=Od.ServiceId

            inner join hospital_mst HM on P.Hospital_ID=HM.Hospital_ID  where P.PatientId=@PatientId  and HM.Hospital_ID=@LocId order by Om.OrdDateTime desc

            End
            Else
            Begin
                    Select -1 as Id,'No Details Available' as Msg
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
