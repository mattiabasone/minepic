<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateAccountsNameChangeTable
 */
class CreateAccountsNameChangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_name_change', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid', 32);
            $table->string('prev_name', 32);
            $table->string('new_name', 32);
            $table->unsignedInteger('time_change');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('accounts_name_change');
    }
}
