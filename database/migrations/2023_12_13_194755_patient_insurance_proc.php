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
    public function up()
    {
        DB::unprepared("
            ---- usp_app_apiPatientInsuranceDetails
            create proc usp_app_apiPatientInsuranceDetails
            (@PatientId int)
            As
            BEGIN
            select top 1 A.AgreementNo As ContractNo, PM.Payee_Name As InsuranceCompany, PH.PolicyHolderName, PLM.PayeePatLevelDesc
            CardNo,
            Case                                                    
                WHEN  (select top 1 EXPDT from PatientInsuranceHistory where  PatientId = @patientID order by ID desc) IS NOT NULL THEN        
                    (select top 1 EXPDT from PatientInsuranceHistory where  PatientId = @patientID order by ID desc)        
            Else                            
            A.ToDate                            
            END AS 'ExpiryDate' ,payeepatientlevellink.OPDeductible_Per,                    
            payeepatientlevellink.DeductibleMaxAmt,payeepatientlevellink.OPPreApprovalAmt   
            
            from Patient P
            inner join PatientInsuranceHistory PIH on P.PatientId = PIH.Patientid
            inner join TPAAgreement A on PIH.AgrementID = A.AgreementID
            inner join tbl_PolicyHolder PH on A.policyholderid = PH.PolicyHolderID
            inner join Payee_Mst PM on A.PayeeID = PM.PayeeId
            inner join payeepatientlevellink on payeepatientlevellink.PayeeId = A.PayeeID                                         
            and payeepatientlevellink.AgreementId =  A.AgreementID                                         
            and payeepatientlevellink.PayeePatientLevelId = PIH.levelID                                        
            and payeepatientlevellink.CaseTypeId = 1
            inner join PayeePatientLevel_Mst PLM on PayeePatLevelId = payeepatientlevellink.PayeePatientLevelId 
            where P.PatientId = @PatientId
            order by PIH.Id desc
            END 
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS usp_app_apiPatientInsuranceDetails');
    }
};
