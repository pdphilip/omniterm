<?php

namespace Workbench\App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use OmniTerm\Samples\AsyncTasksCommand;
use OmniTerm\Samples\BrowserDemoCommand;
use OmniTerm\Samples\CustomColorsCommand;
use OmniTerm\Samples\DataTablesCommand;
use OmniTerm\Samples\FullDemoCommand;
use OmniTerm\Samples\GlobalFunctionsCommand;
use OmniTerm\Samples\InteractiveCommand;
use OmniTerm\Samples\LiveTaskDemoCommand;
use OmniTerm\Samples\ProgressBarsCommand;
use OmniTerm\Samples\SpinnersCommand;
use OmniTerm\Samples\StatusMessagesCommand;
use OmniTerm\Samples\TailwindClassesCommand;
use OmniTerm\Samples\TitleBarsCommand;
use OmniTerm\Samples\VisualElementsCommand;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Route::view('/', 'welcome');

        if ($this->app->runningInConsole()) {
            $this->commands([
                AsyncTasksCommand::class,
                BrowserDemoCommand::class,
                CustomColorsCommand::class,
                DataTablesCommand::class,
                FullDemoCommand::class,
                GlobalFunctionsCommand::class,
                InteractiveCommand::class,
                LiveTaskDemoCommand::class,
                ProgressBarsCommand::class,
                SpinnersCommand::class,
                StatusMessagesCommand::class,
                TailwindClassesCommand::class,
                TitleBarsCommand::class,
                VisualElementsCommand::class,
            ]);
        }
    }
}
