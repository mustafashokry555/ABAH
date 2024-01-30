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
        Schema::dropIfExists('app_company_setting');

        Schema::create('app_company_setting', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value_ar')->nullable();
            $table->text('value_en')->nullable();
            $table->timestamps();

        });

        DB::table('app_company_setting')->insert([
            'key' => 'about_1',
            'value_ar' => 'يعتبر مستشفى علي بن علي من المستشفيات الخاصة بمدينة الرياض والذي تم تأسيسه والعمل على تشغيله مع بدايات عام ٢٠٢٠م وتبلغ السعرة السريرية للمستشفى ١٢٠ سرير، ويطمح أن يكون المستشفى واحداً من أبرز مقدمي خدمات الرعاية الصحية في المملكة العربية السعودية.',
            'value_en' => 'يعتبر مستشفى علي بن علي من المستشفيات الخاصة بمدينة الرياض والذي تم تأسيسه والعمل على تشغيله مع بدايات عام ٢٠٢٠م وتبلغ السعرة السريرية للمستشفى ١٢٠ سرير، ويطمح أن يكون المستشفى واحداً من أبرز مقدمي خدمات الرعاية الصحية في المملكة العربية السعودية.',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('app_company_setting')->insert([
            'key' => 'about_2',
            'value_ar' => 'تتكون الخدمات الصحية في المستشفى بشكل أساسي. من الخدمات التشخيصية والعلاجية والوقائية وخدمات المرافق المساندة للرعاية ، وكما يحرص مستشفى علي بن علي على تقديم الخدمات الصحية بجودة عالية بوجود فريق عالي الكفاءة من الممارسين الصحيين والخدمات الإدارية المتميزة .',
            'value_en' => 'تتكون الخدمات الصحية في المستشفى بشكل أساسي. من الخدمات التشخيصية والعلاجية والوقائية وخدمات المرافق المساندة للرعاية ، وكما يحرص مستشفى علي بن علي على تقديم الخدمات الصحية بجودة عالية بوجود فريق عالي الكفاءة من الممارسين الصحيين والخدمات الإدارية المتميزة .',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('app_company_setting')->insert([
            'key' => 'about_3',
            'value_ar' => 'وقد تم تجهيز المستشفى بأحدث المعدات الطبية ويحتوي المستشفى على مرافق حديثة للمرضى بالعيادات الخارجية والأقسام السريرية مع التركيز على أهمية وجود معايير سلامة المرضى، ويتعبر المستشفى رائداً في مجال الرعاية الصحية المتكاملة من خلال توفيره لأكثر من ٢٠ تخصصاً في مجال الرعاية الصحية .',
            'value_en' => 'وقد تم تجهيز المستشفى بأحدث المعدات الطبية ويحتوي المستشفى على مرافق حديثة للمرضى بالعيادات الخارجية والأقسام السريرية مع التركيز على أهمية وجود معايير سلامة المرضى، ويتعبر المستشفى رائداً في مجال الرعاية الصحية المتكاملة من خلال توفيره لأكثر من ٢٠ تخصصاً في مجال الرعاية الصحية .',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('app_company_setting')->insert([
            'key' => 'twitter',
            'value_ar' => 'https://twitter.com/ABAHospital',
            'value_en' => 'https://twitter.com/ABAHospital',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('app_company_setting')->insert([
            'key' => 'facebook',
            'value_ar' => 'https://www.facebook.com/ABAHospital/',
            'value_en' => 'https://www.facebook.com/ABAHospital/',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('app_company_setting')->insert([
            'key' => 'instagram',
            'value_ar' => 'https://www.instagram.com/aba.hospital?igsh=aTNsaHc2a28zMTlu',
            'value_en' => 'https://www.instagram.com/aba.hospital?igsh=aTNsaHc2a28zMTlu',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('app_company_setting')->insert([
            'key' => 'hotline',
            'value_ar' => '+966545819775',
            'value_en' => '+966545819775',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('app_company_setting')->insert([
            'key' => 'whatsApp',
            'value_en' => 'https://wa.me/+966545819775',
            'value_ar' => 'https://wa.me/+966545819775',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('app_company_setting')->insert([
            'key' => 'mail',
            'value_ar' => 'test.email@gmail.com',
            'value_en' => 'test.email@gmail.com',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_company_setting');
    }
};
