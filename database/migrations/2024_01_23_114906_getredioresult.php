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
            IF OBJECT_ID('usp_app_apiGetPatientRadioResult', 'P') IS NOT NULL
            BEGIN
                DROP PROCEDURE usp_app_apiGetPatientRadioResult;
            END
        ");

        DB::unprepared("
            create proc [dbo].[usp_app_apiGetPatientRadioResult]
            (  
            @OrderDtlid int   
            )                                                                                       
            as                                            
            BEGIN     
                SELECT
                dbo.fnParseRTF(IMGTT.Templatetxt) as RadiologyResult                                                                                                                                 
                FROM                                  
                LABORDERPOSTED LOP                                  
                
                left outer join IMGServices IMGS  WITH (NOLOCK) on LOP.serviceid = IMGS.ServiceID                                   
                left outer join IMGTestTemplate IMGTT  WITH (NOLOCK) on IMGS.TestID = IMGTT.ServiceId and LOP.Ord_BILLID = IMGTT.OrdId
                where                                                          
                LOP.Ord_BILLDTLID=@OrderDtlid 
                
            end    
            
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
