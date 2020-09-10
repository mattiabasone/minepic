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

    /** @var MojangClient */
    private MojangClient $mojangClient;

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
        $this->mojangClient = $mojangClient;
        $this->info('Selecting old uuid...');

        $results = $this->getAccountsIds();

        foreach ($results as $result) {
            /** @var \Minepic\Models\Account $account */
            $account = Account::find($result->id);
            $this->info("Checking {$account->username} [{$account->uuid}]...");
            try {
                $this->updateAccount($account);
                $this->updateAccountSkin($account);
            } catch (\Exception $e) {
                ++$account->fail_count;
                $account->update([
                    'fail_count' => $account->fail_count,
                ]);
                $this->warn("\tFailed. Fail count: {$account->fail_count}");
                if ($account->fail_count > 10) {
                    $account->stats()->delete();
                    $account->delete();
                    $this->error("\tDELETED {$account->uuid}!");
                } else {
                    $account->save();
                }
            }
            $this->line('################################################');
        }

        return 0;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Account[]
     */
    private function getAccountsIds()
    {
        return Account::query()
            ->select(['id'])
            ->whereDate('updated_at', '<', Carbon::now()->subDays(28)->toDateTimeString())
            ->orderBy('updated_at', 'ASC')
            ->take(300)
            ->get();
    }

    /**
     * @param Account $account
     *
     * @throws \Throwable
     */
    private function updateAccount(Account $account)
    {
        $accountApiData = $this->mojangClient->getUuidInfo($account->uuid);
        $this->info("\tUUID Valid");

        // Update database
        $account->update([
            'username' => $accountApiData->getUsername(),
            'skin' => $accountApiData->getSkin(),
            'cape' => $accountApiData->getCape(),
            'fail_count' => 0,
        ]);
        $account->refresh();
        $this->info("\tData updated");
    }

    /**
     * @param Account $account
     *
     * @throws \Throwable
     */
    private function updateAccountSkin(Account $account)
    {
        try {
            $skinData = $this->mojangClient->getSkin($account->skin);
            SkinsStorage::save($account->uuid, $skinData);
            $this->info("\tSkin png updated");
        } catch (\Exception $e) {
            SkinsStorage::copyAsSteve($account->uuid);
            $this->error("\tUsing Steve as skin");
            $this->error("\t".$e->getMessage());
        }
    }
}
