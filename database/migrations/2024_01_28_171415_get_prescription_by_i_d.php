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
            IF OBJECT_ID('usp_app_GetPrescriptionsByID', 'P') IS NOT NULL
            BEGIN
                DROP PROCEDURE usp_app_GetPrescriptionsByID;
            END
        ");


        DB::unprepared("
            create proc usp_app_GetPrescriptionsByID
            @PreID as Int
            AS
            Declare @Count as Int
            Begin

                    select @count=count(*) FROM    dbo.Visit
                    INNER JOIN dbo.Patient ON dbo.Patient.PatientId = dbo.Visit.PatientID
                    LEFT OUTER JOIN dbo.WD_Prescription_Mst ON dbo.WD_Prescription_Mst.PatientID = dbo.Patient.PatientId
                    AND dbo.WD_Prescription_Mst.VisitID = dbo.Visit.Visit_ID
                    INNER JOIN WD_Prescription_Details ON WD_Prescription_Mst.preid = WD_Prescription_Details.preid
                    INNER JOIN ivitemgeneric ON ivitemgeneric.genericid = WD_Prescription_Details.genericid
                    LEFT JOIN UnitMst ON UnitMst.UnitId = WD_Prescription_Details.UnitId
                    INNER JOIN HomeMedicationRoute_Mst ON HomeMedicationRoute_Mst.Id = WD_Prescription_Details.HM_routeId
                    INNER JOIN wd_prescriptionFrequency ON wd_prescriptionFrequency.Frequency_Id = WD_Prescription_Details.Frequency
                    INNER JOIN ivitem ON WD_Prescription_Details.ItemId = ivitem.id 
                    LEFT OUTER JOIN DrugSaleDtl ON DrugSaleDtl.PreDtlId=WD_Prescription_Details.PreDetailID
                    LEFT OUTER JOIN dbo.Ctpl_OpInitialAssessment ON dbo.Ctpl_OpInitialAssessment.VisitId = dbo.Visit.Visit_ID
                    WHERE --(convert(datetime,convert(varchar,dbo.Visit.VisitDate,106)) BETWEEN @FromDate AND @ToDate) 
                    WD_Prescription_Mst.preid = @PreID and ISNULL(WD_Prescription_Details.IsCancelled,0) <> 1

                    If @Count > 0
                    Begin

                        SELECT  
                            dbo.fn_PatientFullName(dbo.Patient.PatientId) AS PatientName ,
                            dbo.fn_DoctorFullName(( dbo.Visit.DocInCharge )) AS DocInCharge ,
                            dbo.fn_GetDocDept(dbo.Visit.DocInCharge) AS Specialty,
                            WD_Prescription_Mst.PreID,WD_Prescription_Details.PreDetailID,Visit.VisitTypeID,WD_Prescription_Mst.PreCode,WD_Prescription_Details.GenericID,
                            convert(varchar,dbo.Visit.VisitDate,105) AS VisitDate ,
                            dbo.Ctpl_OpInitialAssessment.ReasonforConsultation AS Cheif_Complaint,
                            ISNULL(ivitemgeneric.genericname, '') [Generic Name] ,
                            ISNULL(ivitem.name, '') [Medicine] ,
                            WD_Prescription_Details.dosage AS Dosage ,
                            ISNULL(UnitMst.Unitname, '') Unit ,
                            ISNULL(HomeMedicationRoute_Mst.RouteDesc, '') Route ,
                            ISNULL(wd_prescriptionFrequency.frequency_code, '') Frequency ,
                            ISNULL(WD_Prescription_Details.info, 0) AS Info ,
                            ISNULL(WD_Prescription_Details.Duration, 0) Days ,
                            convert(varchar,WD_Prescription_Details.StartDate,105) as StartDate ,
                            convert(varchar,WD_Prescription_Details.EndDate,105) as EndDate,
                            ISNULL(WD_Prescription_Details.reason, '') OrderReason ,
                            convert(varchar,WD_Prescription_Mst.DateTime,105) AS PrescriptionDate,
                            ISNULL(DrugSaleDtl.DetailID,0) as DetailId,
                            1 as Id
                    FROM    dbo.Visit
                            INNER JOIN dbo.Patient ON dbo.Patient.PatientId = dbo.Visit.PatientID
                            LEFT OUTER JOIN dbo.WD_Prescription_Mst ON dbo.WD_Prescription_Mst.PatientID = dbo.Patient.PatientId
                            AND dbo.WD_Prescription_Mst.VisitID = dbo.Visit.Visit_ID
                            INNER JOIN WD_Prescription_Details ON WD_Prescription_Mst.preid = WD_Prescription_Details.preid
                            INNER JOIN ivitemgeneric ON ivitemgeneric.genericid = WD_Prescription_Details.genericid
                            LEFT JOIN UnitMst ON UnitMst.UnitId = WD_Prescription_Details.UnitId
                            INNER JOIN HomeMedicationRoute_Mst ON HomeMedicationRoute_Mst.Id = WD_Prescription_Details.HM_routeId
                            INNER JOIN wd_prescriptionFrequency ON wd_prescriptionFrequency.Frequency_Id = WD_Prescription_Details.Frequency
                            INNER JOIN ivitem ON WD_Prescription_Details.ItemId = ivitem.id 
                            LEFT OUTER JOIN DrugSaleDtl ON DrugSaleDtl.DetailID=WD_Prescription_Details.PreDetailID
                            LEFT OUTER JOIN dbo.Ctpl_OpInitialAssessment ON dbo.Ctpl_OpInitialAssessment.VisitId = dbo.Visit.Visit_ID
                            WHERE --(convert(datetime,convert(varchar,dbo.Visit.VisitDate,106)) BETWEEN @FromDate AND @ToDate) 
                            WD_Prescription_Mst.preid = @PreID and ISNULL(WD_Prescription_Details.IsCancelled,0) <> 1
                    End
                    Else
                    Begin
                        Select -1 as Id,'There are no Prescription details available.' as Msg
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
