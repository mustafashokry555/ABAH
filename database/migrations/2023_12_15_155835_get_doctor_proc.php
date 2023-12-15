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
            create proc [dbo].[usp_app_apiGetAllDoctors]   
            AS  
            DECLARE @Count AS INT  
            
            BEGIN  
            SELECT EM.EmpID AS DoctorId, N'Dr. ' + FirstName + ' ' + MiddleName + ' ' + LastName AS DoctorName  
            ,N'د. ' + R_FirstName + ' ' + R_MiddleName + ' ' + R_LastName AS DoctorNameAr  
                
            ,DM.Department_ID AS DepartmentId  
            ,DM.Department_Name AS DoctorSpeciality  
            ,DM.Department_Name_Arabic AS DoctorSpecialityAr  
            ,AVG(RT.rate) AS AverageRate
            FROM Employee_Mst EM  
            INNER JOIN Department_Mst DM ON EM.Department_ID = DM.Department_ID  
            LEFT JOIN
                app_rate_doctors RT ON EM.EmpID = RT.doctor_id
            WHERE (EM.EmpType = N'Doc')  
            AND EM.Deactive = 0   
            GROUP BY
                EM.EmpID, EM.FirstName, EM.MiddleName, EM.LastName, EM.R_FirstName, EM.R_MiddleName, EM.R_LastName, DM.Department_ID, DM.Department_Name, DM.Department_Name_Arabic
            
            ORDER BY EM.FirstName ASC
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
