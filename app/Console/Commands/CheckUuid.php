<?php

declare(strict_types=1);

namespace Minepic\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Minepic\Helpers\Storage\Files\SkinsStorage;
use Minepic\Minecraft\MojangClient;
use Minepic\Models\Account;

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
     *
     * @param MojangClient $mojangClient
     *
     * @throws \Throwable
     *
     * @return int
     */
    public function handle(MojangClient $mojangClient): int
    {
        $this->info('Selecting old uuid...');

        $results = $this->getAccountsIds();

        foreach ($results as $result) {
            /** @var \Minepic\Models\Account $account */
            $account = Account::find($result->id);
            if ($account) {
                $this->info("Checking {$account->username} [{$account->uuid}]...");
                try {
                    $accountApiData = $mojangClient->getUuidInfo($account->uuid);
                    $this->info("\tUUID Valid");

                    // Update database
                    $account->update([
                        'username' => $accountApiData->getUsername(),
                        'skin' => $accountApiData->getSkin(),
                        'cape' => $accountApiData->getCape(),
                        'fail_count' => 0,
                    ]);
                    $this->info("\tData updated");

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
                    $account->update([
                        'fail_count' => $account->fail_count,
                    ]);
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

        return 0;
    }

    private function getAccountsIds()
    {
        return Account::query()
            ->select(['id'])
            ->whereDate('updated_at', '<', Carbon::now()->subDays(28)->toDateTimeString())
            ->orderBy('updated_at', 'ASC')
            ->take(300)
            ->get();
    }
}
