<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateAccountsStatsTable
 */
class CreateAccountsStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_stats', function (Blueprint $table) {
            $table->string('uuid', 32);
            $table->integer('count_request')->unsigned()->default(0);
            $table->integer('count_search')->unsigned()->default(0);
            $table->timestamp('time_request');
            $table->timestamp('time_search');

            // Index
            $table->primary('uuid');
            $table->index('count_request');
            $table->index('count_search');
            $table->index('time_request');
            $table->index('time_search');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('accounts_stats');
    }
}
