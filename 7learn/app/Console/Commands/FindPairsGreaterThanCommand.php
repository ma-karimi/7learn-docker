<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FindPairsGreaterThanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find:pairs {target} {numbers*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find Pair numbers greater than target number';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $numbers = $this->argument('numbers');
        $target = $this->argument('target');

        $pairs = findPairsGreaterThan($numbers, $target);
        $this->info('Pairs that greater than ' . $target . ' :' . json_encode($pairs));
    }
}
