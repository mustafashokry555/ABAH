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
            IF OBJECT_ID('usp_app_apigetLabPatwiseResults', 'P') IS NOT NULL
            BEGIN
                DROP PROCEDURE usp_app_apigetLabPatwiseResults;
            END
        ");

        DB::unprepared("
            create proc [dbo].[usp_app_apigetLabPatwiseResults]   
            @PatientID AS int 
            AS 
            Declare @Count as Int
            BEGIN              
                
                Select @Count=COUNT(*) FROM                  dbo.OrderMst RIGHT OUTER JOIN                                                    
                                dbo.LABProfileTest INNER JOIN                                                    
                                dbo.LABProfiles ON dbo.LABProfileTest.ProfID = dbo.LABProfiles.ProfID RIGHT OUTER JOIN                                                    
                                dbo.LABTestResult ON dbo.LABProfileTest.ProfTestID = dbo.LABTestResult.TestID AND dbo.LABProfiles.ProfID = dbo.LABTestResult.ProfileID LEFT OUTER JOIN                                                    
                                dbo.LABSample ON dbo.LABTestResult.SampleID = dbo.LABSample.SampleID ON dbo.OrderMst.OrdId = dbo.LABTestResult.OrderID LEFT OUTER JOIN                                          
                                dbo.LabTestParam INNER JOIN                                                    
                                dbo.LABTest ON dbo.LabTestParam.TestID = dbo.LABTest.TestID INNER JOIN                                                    
                                dbo.LABParam ON dbo.LabTestParam.ParamID = dbo.LABParam.ParamID INNER JOIN                                                    
                                dbo.LABHeader ON dbo.LabTestParam.HeaderID = dbo.LABHeader.HeadID LEFT OUTER JOIN                                                    
                                dbo.LABSampleType ON dbo.LABTest.SampleType = dbo.LABSampleType.ID ON dbo.LABTestResult.TestID = dbo.LABTest.TestID AND                                                     
                                dbo.LABTestResult.ParamID = dbo.LABParam.ParamID LEFT OUTER JOIN                                                    
                                                                
                dbo.Orderdtl ON Orderdtl.Orddtlid =  labtestresult.OrderDtlId LEFT OUTER JOIN                                     
                                dbo.LABSampleDtl ON dbo.Labtestresult.SampleDtlID =  dbo.LABSampleDtl.SmpDtlNo        
                Inner Join dbo.patient on dbo.Patient.PatientId=dbo.LABTestResult.PatientID        
            WHERE   LABTestResult.PatientID=@PatientID        
                
            and LABTestResult.Result not like  ''

            If @Count>0
            Begin              
            SELECT   --dbo.patient.PatientId              
            dbo.LABTest.TestName,              
                    dbo.LABParam.ParamName,              
            dbo.LABTestResult.Result,  LABSample.LabNo    ,CONVERT(varchar,LABSample.SampleAccptDtTime,106) as ReportDate   ,
                case (dbo.LABTestResult.ParamNormalRange) when '' then '0-0'  else (dbo.LABTestResult.ParamNormalRange) end as ParamNormalRange,        
                case  when (dbo.LABTestResult.TestFooter) like '%Test Done At%' then 'OutSourced' else (dbo.LABTestResult.TestFooter) end as Remarks,
                LABTestResult.ParamTypeID as ParamType,1 as Id
            FROM                  dbo.OrderMst RIGHT OUTER JOIN                                                    
                                dbo.LABProfileTest INNER JOIN                                                    
                                dbo.LABProfiles ON dbo.LABProfileTest.ProfID = dbo.LABProfiles.ProfID RIGHT OUTER JOIN                                                    
                                dbo.LABTestResult ON dbo.LABProfileTest.ProfTestID = dbo.LABTestResult.TestID AND dbo.LABProfiles.ProfID = dbo.LABTestResult.ProfileID LEFT OUTER JOIN                                                    
                                dbo.LABSample ON dbo.LABTestResult.SampleID = dbo.LABSample.SampleID ON dbo.OrderMst.OrdId = dbo.LABTestResult.OrderID LEFT OUTER JOIN                                          
                                dbo.LabTestParam INNER JOIN                                                    
                                dbo.LABTest ON dbo.LabTestParam.TestID = dbo.LABTest.TestID INNER JOIN                                                    
                                dbo.LABParam ON dbo.LabTestParam.ParamID = dbo.LABParam.ParamID INNER JOIN                                                    
                                dbo.LABHeader ON dbo.LabTestParam.HeaderID = dbo.LABHeader.HeadID LEFT OUTER JOIN                                                    
                                dbo.LABSampleType ON dbo.LABTest.SampleType = dbo.LABSampleType.ID ON dbo.LABTestResult.TestID = dbo.LABTest.TestID AND                                                     
                                dbo.LABTestResult.ParamID = dbo.LABParam.ParamID LEFT OUTER JOIN                                                    
                            --   dbo.LabParamMultiValues ON dbo.LABParam.ParamID = dbo.LabParamMultiValues.ParamID  LEFT OUTER JOIN                                      
                dbo.Orderdtl ON Orderdtl.Orddtlid =  labtestresult.OrderDtlId LEFT OUTER JOIN                                     
                                dbo.LABSampleDtl ON dbo.Labtestresult.SampleDtlID =  dbo.LABSampleDtl.SmpDtlNo        
                Inner Join dbo.patient on dbo.Patient.PatientId=dbo.LABTestResult.PatientID        
                    -- LEFT OUTER JOIN                                 
                                        
            WHERE   LABTestResult.PatientID=@PatientID        
            
            and LABTestResult.Result not like  '' Order by LABSample.SampleAccptDtTime desc      
                End
            Else
            Begin
                Select -1 as Id,
                        'There are No Parameter Details Available' as Msg
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
