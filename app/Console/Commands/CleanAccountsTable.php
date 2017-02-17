<?php
namespace App\Console\Commands;

use App\Database\Accounts;
use Illuminate\Console\Command;

/**
 * Class CleanAccountsTable
 * @package App\Console\Commands
 */
class CleanAccountsTable extends Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'minepic:clean-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean the account table.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->info('Selecting all duplicates...');

        $subQuery = Accounts::select('username', app('db')->raw('COUNT(id) AS total'))
            ->groupBy('username')
            ->orderBy('total', 'DESC')
            ->toSql();

        $results = app('db')->table(app('db')->raw(" ({$subQuery}) AS subq"))
            ->where('total', '>', 1)
            ->get();

        if (count($results) > 0) {
            foreach ($results as $result) {
                $this->info("Removing {$result->username}...");
                $deletedRows = Accounts::where('username', $result->username)->orderBy('updated', 'ASC')->take(1)->delete();
                if ($deletedRows == 1) {
                    $this->info("Deleted");
                } else {
                    $this->error("Error!");
                }
                $this->info("--------------------------------------------");
            }
        } else {
            $this->info("No duplicates found");
        }
    }

}