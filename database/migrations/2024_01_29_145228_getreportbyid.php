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
            IF OBJECT_ID('usp_app_apiPatientMedicalReportById', 'P') IS NOT NULL
            BEGIN
                DROP PROCEDURE usp_app_apiPatientMedicalReportById;
            END
        ");

        DB::unprepared("
            create proc [dbo].[usp_app_apiPatientMedicalReportById]   
            @PatientId INT                  
            AS                  
            BEGIN                  
                            
            SET NOCOUNT ON;                  
                
            declare @CMODrEmpId int = 0        
            select @CMODrEmpId  = isnull(CMODrEmpId,0) from ConfigDefaults        
                                
                                
            SELECT                        
            dbo.Patient.Registration_No, dbo.Visit.VisitNo, dbo.Visit.PatientID, dbo.Visit.VisitTypeID, dbo.Visit.Visit_ID, dbo.Visit.DocInCharge,                   
            (N'Dr. ' + Employee_Mst.FirstName + ' ' + Employee_Mst.MiddleName + ' ' + Employee_Mst.LastName) AS DoctorName  
            ,(N'د. ' + Employee_Mst.R_FirstName + ' ' + Employee_Mst.R_MiddleName + ' ' + Employee_Mst.R_LastName) AS DoctorNameAr  
            ,D1.Department_Name AS DoctorSpeciality  
            ,D1.Department_Name_Arabic AS DoctorSpecialityAr , dbo.Gender_Mst.Description,                   
                dbo.Visit.VisitDate, dbo.Visit.Age, (dbo.Title_Mst.Title_Name + ' ' + dbo.Patient.First_Name + ' ' +   dbo.Patient.Middle_Name + ' ' + dbo.Patient.ThirdName + ' '+    dbo.Patient.Last_Name) AS PtNm ,                
                            
                dbo.Ctpl_OpInitialAssessment.Dischargeto, dbo.Ctpl_OpInitialAssessment.ReasonforConsultation,                   
                        dbo.Ctpl_OpInitialAssessment.Historyofillness,  dbo.Ctpl_OpInitialAssessment.treatmentplan,               
                            
                MaritalStatus_Mst.Status_Name,                              
            case when visit.FollowUp=0 then 'New Visit' else 'Follow up visit' end  as 'New/Follow up',                              
                            
                            
            case when Visit.VisitTypeID<>1 then 'OPD' else 'IPD' end as 'OPD/IPD',                              
                visit.VisitDate,                              
                case when patient.MaritalStatus =1 then 'Yes' else 'No' end as Married,                              
                case when patient.MaritalStatus=2 then 'Yes' else 'No' end as Single,                              
                case when patient.MaritalStatus not in (1,2) then 'Yes' else 'No' end as Others,                              
                case when visit.FollowUp=0 then 1 else 0 end as Newvisit,                              
                case when visit.FollowUp<>0 then 1 else 0 end as Followup,                              
                case when isnull(visit.RefDocID,0) in (0,-1)   then 1  else 0 end as walkin,                              
                case when isnull(visit.RefDocID,0) not in (0,-1) then 1 else 0 end as Referral,                              
                case when visit.VisitTypeID=4 then 1 else 0 end as 'Emergency',                              
                WD_TPRBP_DTLS.Systolic,WD_TPRBP_DTLS.Diastolic,WD_TPRBP_DTLS.Temperature,WD_TPRBP_DTLS.Pulse,                              
                case when Visit.VisitTypeID=1 then visit.ExpectedDuration else AdmissionRequest.ExpectedDuration end as Durationofstay,                              
                case when visit.VisitTypeID<>1 then AdmissionRequest.ReqDateTime else visit.VisitDate end as Expecteddate,                              
                D1.Department_Name As DocDepartment, ReasonforConsultation, Historyofillness, initialDignosis, AssociatedDignosis, Significantsigns,                             
                                    
                (select top 1 ICD_Code from ctpl_OPInitail_Conclusion where dbo.Patient.PatientId = @PatientId and DignosisType_Type = 'INITIAL' order by ICDDiagnosis_Id) As ICD1,                              
                (select top 1 ICD_Code from (select top 2 ICD_Code from ctpl_OPInitail_Conclusion where dbo.Patient.PatientId = @PatientId and DignosisType_Type = 'INITIAL' order by ICDDiagnosis_Id desc) As ICD2) As ICD2,                              
                (select top 1 ICD_Code from (select top 3 ICD_Code from ctpl_OPInitail_Conclusion where dbo.Patient.PatientId = @PatientId and DignosisType_Type = 'INITIAL' order by ICDDiagnosis_Id desc) As ICD3) As ICD3,                              
                (select top 1 ICD_Code from (select top 4 ICD_Code from ctpl_OPInitail_Conclusion where dbo.Patient.PatientId = @PatientId and DignosisType_Type = 'INITIAL' order by ICDDiagnosis_Id desc) As ICD4) As ICD4,                              
                ChronicChk, CongenitalChk, RTAChk, CheckupChk, WorkRelatedChk, PsychiatricChk, InfertilityChk, PregnancyChk, VaccinationChk, LMP  ,                          
            Durationofillness, GeneralExamination, isSportsRelated , Visit.visitage,            
            dbo.fn_DoctorFullName(@CMODrEmpId) AS CMODoctorName,      
            (select dbo.EmployeeSignature.Signature from dbo.EmployeeSignature WHERE Empid =visit.docincharge)  As DocSign,            
            (select dbo.EmployeeSignature.Signature from dbo.EmployeeSignature WHERE Empid = @CMODrEmpId)  As CMOSign  ,    
            MRA.MRArabic    
                                
            FROM         dbo.Visit                  
                            INNER JOIN dbo.Patient ON dbo.Visit.PatientID = dbo.Patient.PatientId                   
                            INNER JOIN  dbo.Gender_Mst ON dbo.Patient.Gender = dbo.Gender_Mst.Gender_ID                   
                INNER JOIN  dbo.Title_Mst ON dbo.Patient.Title = dbo.Title_Mst.Title_ID                  
                inner join dbo.Employee_Mst on Visit.DocInCharge = Employee_Mst.EmpID                            
                inner join dbo.Department_Mst D1 on Employee_Mst.Department_ID = D1.Department_ID                  
                            
                            left join dbo.Ctpl_OpInitialAssessment on Visit.Visit_ID = Ctpl_OpInitialAssessment.VisitId and isnull(Ctpl_OpInitialAssessment.CANCELLED,0)=0 and  isnull(Ctpl_OpInitialAssessment.EntryBy,'D')='D'                         
                left join WD_TPRBP_DTLS on WD_TPRBP_DTLS.Visit_ID=Visit.Visit_ID   and isnull(Entry_From,'T') = 'T'                         
                left join dbo.MaritalStatus_Mst on patient.MaritalStatus=MaritalStatus_Mst.Status_ID                  
                left join AdmissionRequest on AdmissionRequest.VisitID=Visit.Visit_ID                              
                left join EMR_DDMRRecArabic MRA on Visit.Visit_Id = MRA.VisitId            
            WHERE dbo.Patient.PatientId = @PatientId  and isnull(Ctpl_OpInitialAssessment.CANCELLED,0)=0 order by Visit.VisitDate desc     
                            
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
