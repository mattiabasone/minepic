<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Account;
use Illuminate\Console\Command;

/**
 * Class CleanAccountsTable.
 */
class CleanAccountsTable extends Command
{
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
     */
    public function fire()
    {
        $this->info('Selecting all duplicates...');

        $subQuery = Account::select('username', app('db')->raw('COUNT(id) AS total'))
            ->groupBy('username')
            ->orderBy('total', 'DESC')
            ->toSql();

        $results = app('db')->table(app('db')->raw(" ({$subQuery}) AS subq"))
            ->where('total', '>', 1)
            ->get();

        if (\count($results) > 0) {
            foreach ($results as $result) {
                $this->info("Removing {$result->username}...");
                $deletedRows = Account::where('username', $result->username)->orderBy('updated', 'ASC')->take(1)->delete();
                if ($deletedRows == 1) {
                    $this->info('Deleted');
                } else {
                    $this->error('Error!');
                }
                $this->info('--------------------------------------------');
            }
        } else {
            $this->info('No duplicates found');
        }
    }
}
