<?php

namespace App\Console;

use App\Console\Commands\DeleteExpiredLinks;
use App\Console\Commands\ResetDemoSite;
use Common\Generators\Action\GenerateAction;
use Common\Generators\Controller\GenerateController;
use Common\Generators\Model\GenerateModel;
use Common\Generators\Policy\GeneratePolicy;
use Common\Generators\Request\GenerateRequest;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        GenerateController::class,
        GenerateModel::class,
        GeneratePolicy::class,
        GenerateRequest::class,
        GenerateAction::class,
        ResetDemoSite::class,
        DeleteExpiredLinks::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('links:delete_expired')
            ->daily();

        if (config('common.site.demo')) {
            $schedule->command('demoSite:reset')
                ->daily();
        }
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        // require base_path('routes/console.php');
    }
}
