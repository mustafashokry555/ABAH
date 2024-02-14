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
            IF OBJECT_ID('usp_app_apiGetAllDepartments', 'P') IS NOT NULL
            BEGIN
                DROP PROCEDURE usp_app_apiGetAllDepartments;
            END
        ");

        DB::unprepared("
            create proc [dbo].[usp_app_apiGetAllDepartments]   
            AS
            BEGIN
                SELECT DM.Department_ID as DepartmentId
                    ,DM.Department_Name as DepartmentName
                    ,DM.Department_Name_Arabic as DepartmentNameAr
                FROM Department_Mst DM
                inner join OPDClinic_Mst OC on DM.Department_ID = OC.DepartmentID
                WHERE DM.Deactive = 0 and DM.Department_Name_Arabic IS NOT NULL
                    order by DM.Department_Name
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
