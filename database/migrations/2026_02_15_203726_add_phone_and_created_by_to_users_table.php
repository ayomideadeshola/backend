<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->after('email');
            $table->enum('created_by', ['self', 'admin'])->default('self')->after('password');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->after('created_by');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'created_by', 'created_by_user_id']);
        });
    }
};