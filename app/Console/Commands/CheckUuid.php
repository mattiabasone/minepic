<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Database\Accounts;
use App\Helpers\Storage\Files\SkinsStorage;
use App\Minecraft\MojangClient;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Class CleanAccountsTable.
 */
class CheckUuid extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'minepic:check-uuid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check old uuid.';

    /**
     * Execute the console command.
     */
    public function fire()
    {
        $this->info('Selecting old uuid...');

        $timeCheck = Carbon::now()->subDays(28);

        $results = Accounts::select('id')
            ->whereDate('updated_at', '<', $timeCheck->toDateTimeString())
            ->orderBy('updated', 'ASC')
            ->take(300)
            ->get();

        $mojangClient = new MojangClient();
        if (\count($results) > 0) {
            foreach ($results as $result) {
                $account = Accounts::find($result->id);
                if ($account) {
                    $this->info("Checking {$account->username} [{$account->uuid}]...");
                    try {
                        $accountApiData = $mojangClient->getUuidInfo($account->uuid);
                        $this->info("\tUUID Valid");

                        // Update database
                        $account->username = $accountApiData->username;
                        $account->skin = $accountApiData->skin;
                        $account->cape = $accountApiData->cape;
                        $account->fail_count = 0;
                        $account->updated = \time();
                        if ($account->save()) {
                            $this->info("\tData updated");
                        }

                        try {
                            $skinData = $mojangClient->getSkin($account->uuid);
                            SkinsStorage::save($account->uuid, $skinData);
                            $this->info("\tSkin png updated");
                        } catch (\Exception $e) {
                            SkinsStorage::copyAsSteve($account->uuid);
                            $this->error("\tUsing Steve as skin");
                            $this->error("\t".$e->getMessage());
                        }
                    } catch (\Exception $e) {
                        ++$account->fail_count;
                        $account->updated = \time();
                        $this->warn("\tFailed. Fail count: {$account->fail_count}");
                        if ($account->fail_count > 10) {
                            $account->delete();
                            $this->error("\tDELETED!");
                        } else {
                            $account->save();
                        }
                    }
                    $this->line('################################################');
                }
            }
        } else {
            $this->info('No old uuid found');
        }
    }
}
