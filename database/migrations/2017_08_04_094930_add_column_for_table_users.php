<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnForTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
		$table->string("gender");
   		$table->string("salt");
		$table->string("encrypted_password");
		$table->integer("is_login");
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
            $table->dropColumn("salt"); $table->dropColumn("encrypted_password"); $table->dropColumn("is_login"); $table->dropColumn("gender");
        });
    }
}
