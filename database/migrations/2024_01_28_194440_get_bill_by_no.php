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
            IF OBJECT_ID('usp_app_GetBillByNo', 'P') IS NOT NULL
            BEGIN
                DROP PROCEDURE usp_app_GetBillByNo;
            END
        ");


        DB::unprepared("
            create proc usp_app_GetBillByNo
            @BillNo as nvarchar(50)    
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
            Where BM.BillNo = @BillNo
                
                
            -- [dbo].fn_DoctorfullName(V.DocInCharge) as DoctorName,BM.BillNo,BM.BillDate,DM.Department_Name,    
            --Quantity,Rate,Amount,[Service_Name] as ServiceName,NetAmount as TotalAmount    
                
            --select top 10 * from BillingDetails    
            If @Count>0    
            Begin    
            Select [dbo].fn_DoctorfullName(V.DocInCharge) as DoctorName,BM.BillNo,BM.BillDate,DM.Department_Name,    
            Quantity,Rate,Amount,[Service_Name] as ServiceName,NetAmount as TotalAmount,1 as Id,'Invoice Detail' as Msg    
            from Billing_Mst BM inner join BillingDetails BD on BM.Bill_ID=BD.Bill_ID    
            inner join Visit V on V.Visit_Id=BM.Visit_ID    
            inner join Patient P on P.Patientid=V.PatientId    
            inner join Employee_Mst EM on EM.EmpId=V.DocInCharge    
            inner join Department_Mst DM on DM.Department_Id=V.DepartmentId    
            inner join Service_mst SM on BD.ServiceId=SM.Service_Id    
            Where BM.BillNo = @BillNo   
            End    
            Else    
            Begin    
            Select -1 as Id,'No Invoice Details Available.' as Msg    
            End    
                
            End  
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
