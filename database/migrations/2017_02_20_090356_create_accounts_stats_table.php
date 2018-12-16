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
            $table->unsignedInteger('count_request')->default(0);
            $table->unsignedInteger('count_search')->default(0);
            $table->unsignedInteger('time_request');
            $table->unsignedInteger('time_search');

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
