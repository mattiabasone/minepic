<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Database\Accounts;
use App\Helpers\Storage\Files\SkinsStorage;
use App\Minecraft\MojangClient;
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

        $results = Accounts::select('id')
            ->where('updated', '<', (\time() - 2419200))
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
                        $this->info('    UUID Valid    ');

                        // Update database
                        $account->username = $accountApiData->username;
                        $account->skin = $accountApiData->skin;
                        $account->cape = $accountApiData->cape;
                        $account->fail_count = 0;
                        $account->updated = \time();
                        if ($account->save()) {
                            $this->info('    Data updated    ');
                        }

                        try {
                            $skinData = $mojangClient->getSkin($account->uuid);
                            SkinsStorage::save($account->uuid, $skinData);
                            $this->info('    Skin png updated   ');
                        } catch (\Exception $e) {
                            SkinsStorage::copyAsSteve($account->uuid);
                            $this->error('    Using Steve as skin    ');
                            $this->error('    '.$e->getMessage());
                        }
                    } catch (\Exception $e) {
                        ++$account->fail_count;
                        $account->updated = \time();
                        $this->warn("    Failed. Fail count: {$account->fail_count}    ");
                        if ($account->fail_count > 10) {
                            $account->delete();
                            $this->error('    DELETED!    ');
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
