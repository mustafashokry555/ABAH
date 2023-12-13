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
            CREATE PROCEDURE usp_app_apiGetDocAppSlots

            @pEmpID AS INT,                                                                                                                                                        
            @DtFrom as DateTIME,                                                                                
            @DtTo as DateTIME ,                                                                                
            @plocid  as Char                                                                              
            As                                                                                          
            BEGIN                                                                     
            SET NOCOUNT ON;                                                

            Select Distinct 'App' As Type, DM.Days,detail.DtId,detail.Dt_dtlId,                                                                  
            detail.Tmfrm as FromTime ,detail.tmto as ToTime,detail.Slot,                                                                            
            CONVERT(date, @DTFROM,103)as [DATE]                                                                        
            , ISNULL(DD.IsBlock,0) as [Block],ISNULL(DD.DtBlockId,0) as DtBlockId ,                                                            
            CASE WHEN ISNULL(DD.IsBlock,0) = 1 THEN 'Blocked Slot' ELSE   ' ' END as sub ,                                                
            App.Id  As Registration_No , 0 as  PatientId, 0 as VisitId  , @DTFROM as FromDateTime                                                           
            into #Temp1                                                        
            from doctiming_dtl detail                                 
            join  Doctiming_Mst DM on detail.dtid = Dm.dtid                                                                              
            LEFT JOIN  DocTiming_Block DD on  (DM.DtId = DD.DtMstId                                                                     
            and detail.Dt_dtlId = DD.Dt_dtlId and DD.BlockDate = CONVERT(date,@DTFROM,103))                                      
            LEFT JOIN Ds_PatientAppoinmentTemperary APP ON CONVERT(VARCHAR(5),FromTime,108) = CONVERT(VARCHAR(5),detail.Tmfrm,108) and APP.DoctorId = @pEmpID                                       
            and APP.app_date =  CONVERT(date,@DTFROM,103)  and ltrim(isnull(App.ApntStatus,''))!='CANCELED'                                                                 
            where DM.Doctorid=@pEmpID And DM.Locid= @plocid                                                                                 
            and (CONVERT(date,@DTFROM,103) BETWEEN  CONVERT(date,DM.DTFRM,103) AND  CONVERT(date,DM.DTTO,103))                                                                            
            and DATENAME(dw,@DTFROM) = DM.DAYS                
            and DM.DoctorId not in (select DoctorId from UnitAvailableTimings where (CONVERT(date,@DTFROM,103) BETWEEN  CONVERT(date,FromDate,103) AND  CONVERT(date,ToDate,103)) and DoctorId = Dm.DoctorId and Leave_Type='L')              
            order by dtid, dt_dtlid;                                

            Select *,                                              
            Case StatusId                                                
            When 0 THEN (select top 1 Color from DBoardStatus where isnull(Deactive,0)=0 and DBStatusId=0)                                              
            When 1 THEN (select top 1 Color from DBoardStatus where isnull(Deactive,0)=0 and DBStatusId=1)                                              
            When 2 THEN (select top 1 Color from DBoardStatus where isnull(Deactive,0)=0 and DBStatusId=2)                                              
            When 3 THEN (select top 1 Color from DBoardStatus where  DBStatusId=3)                                              
            When 4 THEN (select top 1 Color from DBoardStatus where  DBStatusId=4)                                  
            When 5 THEN (select top 1 Color from DBoardStatus where isnull(Deactive,0)=0 and DBStatusId=5)                                              
            When 6 THEN (select top 1 Color from DBoardStatus where isnull(Deactive,0)=0 and DBStatusId=6)                                              
            When 7 THEN (select top 1 Color from DBoardStatus where isnull(Deactive,0)=0 and DBStatusId=7)                                              
            When 8 THEN (select top 1 Color from DBoardStatus where isnull(Deactive,0)=0 and DBStatusId=8)                                              
            Else                             
            ('0, 0, 0')                                              
            End as ColorType                          
            from (                                                  
            Select Distinct Type, Days,DtId,Dt_dtlId,T1.FromTime,T1.ToTime ,Slot,T1.[DATE], [Block],DtBlockId,                                                                         
            REPLACE(ISNULL(                                                            
            CASE WHEN isnull(APP.PatientId,0) = 0 AND isnull(T1.REGISTRATION_NO,0) != 0 THEN                                                        
            isnull(APP.AppType,'R') + ' - UNREGISTERED ' +                                           
            ' Patient: '+ isnull(APP.Firstname,'') + ' ' + isnull(APP.Middlename,'') + ' ' + isnull(APP.LastName,'')  + ' ' + isnull(APP.ThirdName,'') + ' Status:' + isnull(APP.ApntStatus,'Not Confirmed') + ' Mob.:' + isnull(APP.Mobile,0)                           
            ELSE                                                               
            isnull(APP.AppType,'R')+ ' - ' +CONVERT(varchar(20), P.REGISTRATION_NO) +                                                        
            ' Patient: '+ isnull(APP.Firstname,'') + ' ' + isnull(APP.Middlename,'') + ' ' + isnull(APP.LastName,'') +  ' '  +  isnull(APP.ThirdName,'') +   ' ' +                                                           
            isnull(P.R_FirstName,'') + ' ' + isnull(P.R_MiddleName,'') + ' ' + isnull(P.R_ThirdName,'') + ' ' + isnull(P.R_FamilyName,'') + ' Status:' + isnull(APP.ApntStatus,'Not Confirmed') + ' Mob.:' + isnull(P.Mobile,0)
            END,T1.sub),'''','')                                                 
            as sub,                                                            
            CASE WHEN isnull(APP.PatientId,0) = 0 AND isnull(T1.REGISTRATION_NO,0) != 0                                                         
            THEN 'UNREGISTERED' ELSE CONVERT(varchar(20), P.REGISTRATION_NO) END As Registration_No                                                            
            , isnull(APP.PatientId,0) as  PatientId,                    
            isnull(APP.VisitId,0)  as VisitId,
            CASE WHEN isnull(QMD.UpdatedBy,0) != 0 AND isnull(QMD.Status,'')='Call'  THEN 7                                                   
            WHEN isnull(QMT.UpdatedBy,0) != 0 THEN 6                                                   
            WHEN isnull(T1.Block,0) = 1 THEN 8                                                   
            WHEN isnull(APP.VisitId,0) !=0  THEN 5 
            WHEN isnull(T1.REGISTRATION_NO,'') = '' THEN 0                                                      
            WHEN  isnull(APP.PatientId,0) != 0 AND ISNULL(ApntStatus,'')='CONFIRMED' THEN 1                                                      
            WHEN isnull(APP.PatientId,0) != 0 AND ISNULL(ApntStatus,'') = '' THEN 2                                                     
            WHEN isnull(APP.PatientId,0) = 0 AND ISNULL(ApntStatus,'')='CONFIRMED' THEN 3                                                
            WHEN isnull(APP.PatientId,0) = 0 AND ISNULL(ApntStatus,'') = '' THEN 4                                                  
            END  as StatusId   ,ISNULL(APP.ID,0) as 'AppID', ISNULL((Select Top 1 TokenDetailId from QMTokenDetails Where TokenMasterId = QMT.TokenMasterId order by TokenDetailId desc),0) as TokenDetailsId

            , Concat(FORMAT(T1.FromTime, 'hh:mm tt'),  ' To ',FORMAT(T1.ToTime, 'hh:mm tt')) As [SlotsTime]  , T1.FromDateTime, iSNULL(APP.ParentSlotId,0) ParentSlotId                                            
            , V.VisitTypeID , V.VisitDate , ISNULL(App.FollowupVisit ,0) As AppFollowUpVisit                          
            from #Temp1 T1                                                                     
            left outer join Ds_PatientAppoinmentTemperary APP on Isnull(T1.Registration_No,0) = APP.ID and isnull(ApntStatus,'') != 'CANCELED'                  
            and APP.DoctorId=@pEmpID and  (CONVERT(date,T1.DATE,103) BETWEEN  CONVERT(date,APP.app_date,103) AND  CONVERT(date,APP.app_date,103))                  
            left outer join Patient P on APP.PatientID = P.PatientId                                                           
            left outer join Ds_PatientAppoinment APPC on APP.Id = APPC.Id and APP.PatientID = APPC.PatientId and APP.DoctorId = APPC.DoctorID                                                    
            left outer join Visit V on (APP.VisitId = V.Visit_Id and isnull(V.Cancelled,0) = 0 )                                                
            LEFT OUTER join QMTokenMaster  QMT on APP.VisitId = QMT.Visit_ID                                                
            Left Outer join QMTokenDetails  QMD on QMD.TokenMasterId = QMT.TokenMasterId        

            ) As Tbl                                              
            END    
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS usp_app_apiGetDocAppSlots');
    }
};
