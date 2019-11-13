<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimestampsToAccountsNameChange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts_name_change', function (Blueprint $table) {
            $table->timestamps();
        });

        $changes = DB::table('accounts_name_change')
            ->select(['id', 'time_change'])->get();
        foreach ($changes as $change) {
            $carbon = \Carbon\Carbon::createFromTimestamp($change->time_change);
            DB::table('accounts_name_change')
                ->where('id', '=', $change->id)
                ->update([
                    'created_at' => $carbon->format('Y-m-d H:i:s'),
                    'updated_at' => $carbon->format('Y-m-d H:i:s'),
                ]);
        }

        Schema::table('accounts_name_change', function (Blueprint $table) {
            $table->dropColumn('time_change');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts_name_change', function (Blueprint $table) {
            $table->unsignedInteger('time_change')->nullable();
            $table->dropTimestamps();
        });
    }
}
