<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class OptimizeAllCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimize:all {--f|force : Force optimize.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize all.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (! $this->getLaravel()->isProduction() && ! $this->option('force')) {
            return self::INVALID;
        }

        $resourceUsage = catch_resource_usage(function () {
            Process::fromShellCommandline('composer dump-autoload --optimize --ansi')
                ->mustRun(function (string $type, string $line): void {
                    $this->output->write($line);
                });
            $this->call('config:cache');
            $this->call('event:cache');
            $this->call('route:cache');
            $this->call('view:cache');
        });

        $this->output->success($resourceUsage);

        return self::SUCCESS;
    }
}
