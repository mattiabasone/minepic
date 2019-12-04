<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimestampsColumnsToAccountsNotFoundTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts_not_found', function (Blueprint $table) {
            $table->timestamps();
        });

        $results = \DB::table('accounts_not_found')->select(['request', 'time'])->get();
        foreach ($results as $result) {
            \DB::table('accounts_not_found')->update([
                'created_at' => $result->time,
                'updated_at' => $result->time,
            ]);
        }

        Schema::table('accounts_not_found', function (Blueprint $table) {
            $table->dropColumn('time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts_not_found', function (Blueprint $table) {
            $table->timestamp('time');
        });

        $results = \DB::table('accounts_not_found')->select(['request', 'updated_at'])->get();
        foreach ($results as $result) {
            \DB::table('accounts_not_found')->update([
                'time' => $result->updated_at,
            ]);
        }

        Schema::table('accounts_not_found', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
}
