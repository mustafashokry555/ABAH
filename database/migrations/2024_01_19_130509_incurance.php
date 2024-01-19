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
            IF OBJECT_ID('usp_app_apiPatientApprovalDtls', 'P') IS NOT NULL
            BEGIN
                DROP PROCEDURE usp_app_apiPatientApprovalDtls;
            END
        ");

        DB::unprepared("
            create proc [dbo].[usp_app_apiPatientApprovalDtls]   
            (@PatientId int=0)

            As

            BEGIN

            select TD.ApprovedDtlID, TM.ApprovalDate, 
            SM.Service_Name,

            case when isnull(TD.Approved,0) = 1 then
            case isnull(TM.ApprovedStatus,'') 
            when 'C' then 'Cancelled' 
            when 'A' then 'Approved'
            when 'PA' then 'Partial Approved'
            when 'R' then 'Rejected'
            else 'UnKnown'
            end 
            else 
            case isnull(TM.ApprovedStatus,'') 
            when 'C' then 'Cancelled' 
            when 'A' then 'Rejected'
            when 'PA' then 'Partial Approved'
            when 'R' then 'Rejected'
            else 'UnKnown'
            end 
            end
            As ApprovalStatus,
            DM.Department_Name as Department_EN, DM.Department_Name_Arabic as Department_AR,
            TM.InsertedON as create_at
            from TPABillingApprovalDtl TD
            inner join TPABillingApprovalMst TM on TD.BillApprovalId = TM.BillApprovalId
            left join Service_mst SM on TD.ServiceId = SM.Service_ID
            left join Department_Mst DM on SM.DepartmentId = DM.Department_ID
            left join IVItem IM on TD.DrugId = IM.ID
            where TM.PatientId = @PatientId
            order by TM.BillApprovalId desc
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
