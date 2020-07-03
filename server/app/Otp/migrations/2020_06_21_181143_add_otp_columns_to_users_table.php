<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtpColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile_number')
                ->after('email');
            $table->string('google2fa_secret')
                ->nullable()
                ->after('password');
            $table->boolean('is_google2fa_activated')
                ->default(false)
                ->after('password');
            $table->string('pin')
                ->after('password')
                ->nullable();
            $table->string('otp')
                ->nullable()
                ->after('password');
            $table->enum('otp_type', ['pin', 'mail', 'sms', 'google2fa'])
                ->default('pin')
                ->after('password');
            $table->boolean('is_otp_verification_enabled_at_login')
                ->default(false)
                ->after('password');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('mobile_number');
            $table->dropColumn('is_otp_verification_enabled_at_login');
            $table->dropColumn('otp_type');
            $table->dropColumn('otp');
            $table->dropColumn('pin');
            $table->dropColumn('is_google2fa_activated');
            $table->dropColumn('google2fa_secret');
        });
    }
}
