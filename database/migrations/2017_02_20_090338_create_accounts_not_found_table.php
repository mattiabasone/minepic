<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateAccountsNotFoundTable
 */
class CreateAccountsNotFoundTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_not_found', function (Blueprint $table) {
            $table->string('request', 32);
            $table->timestamp('time');

            // Index
            $table->primary('request');
            $table->index('time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('accounts_not_found');
    }
}
