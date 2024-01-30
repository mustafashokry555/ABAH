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
            IF OBJECT_ID('usp_app_BollByID', 'P') IS NOT NULL
            BEGIN
                DROP PROCEDURE usp_app_BollByID;
            END
        ");

        DB::unprepared("
            create proc [dbo].[usp_app_BollByID]   
            @RegistrationNo as nvarchar(50),    
            @LocId as Int   
            AS    
            Declare @Count as Int    
            Begin    
                
            Select @Count=count(*)      
            from Billing_Mst BM inner join BillingDetails BD on BM.Bill_ID=BD.Bill_ID    
            inner join Visit V on V.Visit_Id=BM.Visit_ID    
            inner join Patient P on P.Patientid=V.PatientId    
            inner join Employee_Mst EM on EM.EmpId=V.DocInCharge    
            inner join Department_Mst DM on DM.Department_Id=V.DepartmentId    
            inner join Service_mst SM on BD.ServiceId=SM.Service_Id    
            Where BM.Registration_No=@RegistrationNo   
                
                
            -- [dbo].fn_DoctorfullName(V.DocInCharge) as DoctorName,BM.BillNo,BM.BillDate,DM.Department_Name,    
            --Quantity,Rate,Amount,[Service_Name] as ServiceName,NetAmount as TotalAmount    
                
            --select top 10 * from BillingDetails    
            If @Count>0    
            Begin    
            Select (N'Dr. ' + EM.FirstName + ' ' + EM.MiddleName + ' ' + EM.LastName) AS DoctorName  
                        ,(N'د. ' + EM.R_FirstName + ' ' + EM.R_MiddleName + ' ' + EM.R_LastName) AS DoctorNameAr  
                        ,DM.Department_Name AS DoctorSpeciality  
                        ,DM.Department_Name_Arabic AS DoctorSpecialityAr,BM.BillNo,BM.BillDate,    
            Quantity,Rate,Amount,[Service_Name] as ServiceName,NetAmount as TotalAmount,1 as Id,'Invoice Detail' as Msg    
            from Billing_Mst BM inner join BillingDetails BD on BM.Bill_ID=BD.Bill_ID    
            inner join Visit V on V.Visit_Id=BM.Visit_ID    
            inner join Patient P on P.Patientid=V.PatientId    
            inner join Employee_Mst EM on EM.EmpId=V.DocInCharge    
            inner join Department_Mst DM on DM.Department_Id=V.DepartmentId    
            inner join Service_mst SM on BD.ServiceId=SM.Service_Id    
            Where BM.Registration_No=@RegistrationNo order by BM.BillDate desc
            End    
            Else    
            Begin    
            Select -1 as Id,'No Invoice Details Available.' as Msg    
            End    
                
            End
        ");
        //  
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
