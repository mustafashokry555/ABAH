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
        DB::unprepared('
            ---- usp_app_apiLoginRegNo
            CREATE PROCEDURE usp_app_apiLoginRegNo
            @userId As  NvarChar(100),  
            @userPassword As NvarChar(100)
            
            AS                          
            declare @vr_cnt int,                          
            @pUIDStatus int,
            @dDob datetime,
            @Age int,
            @empId Int,
            @RegistrationNo nvarchar(50) , 
            @PasswordExpiryDate datetime,
            @Deactive bit,
            @OTPCount Int,
            @SessionTimeout int=5,
            @FeedBackTime Int
            BEGIN    
            set @vr_cnt = 0                          
            set @pUIDStatus = 0                  
            Select @FeedBackTime=FeedBackTimeout from ConfigDefaults                                     
                        
            begin              
            Select @Deactive=Deactive,@OTPCount=0 from Patient where  UPPER(registration_no)=@userId        
            SELECT @pUIDStatus = count(*) FROM patient WHERE                        
            UPPER(registration_no)=@userId              
                        
            SELECT @vr_cnt = count(*) FROM patient WHERE                           
            UPPER(registration_no)=@userId   
            and (patient_password) = (@userPassword) and  Deactive=0                          
                    
                Select @RegistrationNo=Registration_No,@PasswordExpiryDate=ISNULL(PassExpDate,GETDATE()) from Patient where
                Registration_No=@userId and   (patient_password) = (@userPassword) and Deactive=0                          
            
            begin
            If @Deactive=1
            Begin 
                SELECT -2 as  "Id"  
            , "" "UserName"   
            , "" "Name"  
                , -1 "EmpId",
                "Your account has been deactivated,Please contact hospital to activate your account." as Msg
            , @pUIDStatus as "loginUID"
            End 
            Else
            Begin
            If ISNULL(NULLIF(@OTPCount,0),1) <> 4
            Begin
            If @vr_cnt=1
            Begin
                Select PatientId, Registration_No from Patient where
                Registration_No=@userId and (patient_password) = (@userPassword) and Deactive=0
                End
                else
                begin
                Select -1 as Id,"Your credentials do not match our records" as Msg
                end
                End
                Else
                Begin
                    SELECT -1 "Id"  
                , "" "UserName"   
                , "" "Name"  
                    , -3 "EmpId"  
                , @pUIDStatus as "loginUID",
                "You have exceeded your maximum OTP Request Count,you can not login again.Please contact Hospital to reset your account" as Msg
                End
            end              
                    
            end   
            End
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS usp_app_apiLoginRegNo');
    }
};
