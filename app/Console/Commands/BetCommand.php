<?php
namespace App\Console\Commands;

use App\Services\BetService;
use App\Services\SymbolService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class BetCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bet:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command to execute one bet game";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $betService = new BetService();
            $betService->doBet();
            $this->info($betService->getBetResult());
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }

    }
}
