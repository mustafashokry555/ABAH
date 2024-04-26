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
        DB::table('User_Mst')->where('UserID', '1000')->where('UserName', 'mobileApp')->delete();

        DB::table('User_Mst')->insert([
            'UserID' => '1000',
            'UserName' => 'mobileApp',
        ]);

        DB::unprepared("
            IF OBJECT_ID('usp_app_apiLoginRegNo', 'P') IS NOT NULL
            BEGIN
                DROP PROCEDURE usp_app_apiLoginRegNo;
            END
        ");
        

        DB::unprepared("
            CREATE PROCEDURE usp_app_apiLoginRegNo
                @userId NVARCHAR(100),
                @userPassword NVARCHAR(100)
            AS
            BEGIN
                DECLARE @vr_cnt INT,
                        @pUIDStatus INT,
                        @dDob DATETIME,
                        @Age INT,
                        @empId INT,
                        @RegistrationNo NVARCHAR(50),
                        @PasswordExpiryDate DATETIME,
                        @Deactive BIT,
                        @OTPCount INT,
                        @SessionTimeout INT = 5,
                        @FeedBackTime INT;

                SET @vr_cnt = 0;
                SET @pUIDStatus = 0;

                SELECT @FeedBackTime = FeedBackTimeout FROM ConfigDefaults;

                SELECT @Deactive = Deactive, @OTPCount = 0 FROM Patient WHERE UPPER(registration_no) = @userId;

                SELECT @pUIDStatus = COUNT(*) FROM Patient WHERE UPPER(registration_no) = @userId;

                SELECT @vr_cnt = COUNT(*) FROM Patient WHERE UPPER(registration_no) = @userId
                    AND (patient_password) = (@userPassword) AND Deactive = 0;

                SELECT @RegistrationNo = Registration_No, @PasswordExpiryDate = ISNULL(PassExpDate, GETDATE())
                FROM Patient WHERE Registration_No = @userId AND (patient_password) = (@userPassword) AND Deactive = 0;

                IF @Deactive = 1
                BEGIN
                    SELECT -2 AS 'Id', -1 AS 'EmpId', 'Your account has been deactivated. Please contact the hospital to activate your account.' AS 'Msg', @pUIDStatus AS 'loginUID';
                END
                ELSE
                BEGIN
                    IF ISNULL(NULLIF(@OTPCount, 0), 1) <> 4
                    BEGIN
                        IF @vr_cnt = 1
                        BEGIN
                            SELECT PatientId, Registration_No FROM Patient WHERE Registration_No = @userId
                                AND (patient_password) = (@userPassword) AND Deactive = 0;
                        END
                        ELSE
                        BEGIN
                            SELECT -1 AS Id, 'The password is wrong!' AS Msg;
                        END
                    END
                    ELSE
                    BEGIN
                        SELECT -1 AS 'Id', -3 AS 'EmpId', @pUIDStatus AS 'loginUID', 'You have exceeded your maximum OTP Request Count. You cannot log in again. Please contact the Hospital to reset your account' AS Msg;
                    END
                END
            END
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
