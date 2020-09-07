<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AccountStatsTableV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts_stats', function (Blueprint $table) {
            $table->dropColumn('count_search');
            $table->dropColumn('time_search');
            $table->timestamp('request_at')->nullable();
            $table->index('request_at');
        });

        $results = DB::table('accounts_stats')
            ->select(['uuid', 'time_request'])
            ->get();

        foreach ($results as $result) {
            if ($result->time_request) {
                DB::table('accounts_stats')
                    ->where('uuid', '=', $result->uuid)
                    ->update([
                        'request_at' => \Carbon\Carbon::createFromTimestamp($result->time_request)
                            ->format('Y-m-d H:i:s')
                    ]);
            }
        }

        Schema::table('accounts_stats', function (Blueprint $table) {
            $table->dropColumn('time_request');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts_stats', function (Blueprint $table) {
            $table->dropColumn('request_at');
            $table->unsignedInteger('count_search')->default(0)->after('count_request');
            $table->unsignedInteger('time_request');
            $table->unsignedInteger('time_search');

            $table->index('count_search');
            $table->index('time_request');
            $table->index('time_search');
        });
    }
}
