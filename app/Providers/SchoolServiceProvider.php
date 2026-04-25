<?php

namespace Modules\School\Providers;

use App\Services\MenuService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class SchoolServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'School';

    protected string $nameLower = 'school';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
        $this->registerMenuItems();
    }

    /**
     * Register menu items for the School module.
     */
    protected function registerMenuItems(): void
    {
        $this->app->booted(function () {
            try {
                MenuService::addMenuItem(
                    menu: 'primary',
                    id: 'school',
                    title: __('Schools'),
                    url: route('school.schools.index'),
                    icon: 'GraduationCap',
                    order: 40,
                    permissions: 'schools.view_any',
                    route: 'school.*'
                );

                MenuService::addSubmenuItem(
                    'primary',
                    'school',
                    __('All Schools'),
                    route('school.schools.index'),
                    10,
                    'schools.view_any',
                    'school.schools.*',
                    'Building2'
                );

                // Only add submenu items if routes exist
                if (\Route::has('school.departments.index')) {
                    MenuService::addSubmenuItem(
                        'primary',
                        'school',
                        __('Departments'),
                        route('school.departments.index'),
                        20,
                        'departments.view_any',
                        'school.departments.*',
                        'Layers'
                    );
                }

                if (\Route::has('school.programs.index')) {
                    MenuService::addSubmenuItem(
                        'primary',
                        'school',
                        __('Programs'),
                        route('school.programs.index'),
                        30,
                        'programs.view_any',
                        'school.programs.*',
                        'BookOpen'
                    );
                }

                if (\Route::has('school.courses.index')) {
                    MenuService::addSubmenuItem(
                        'primary',
                        'school',
                        __('Courses'),
                        route('school.courses.index'),
                        40,
                        'courses.view_any',
                        'school.courses.*',
                        'FileText'
                    );
                }

                if (\Route::has('school.classrooms.index')) {
                    MenuService::addSubmenuItem(
                        'primary',
                        'school',
                        __('Classrooms'),
                        route('school.classrooms.index'),
                        50,
                        'classrooms.view_any',
                        'school.classrooms.*',
                        'DoorOpen'
                    );
                }

                if (\Route::has('school.equipment.index')) {
                    MenuService::addSubmenuItem(
                        'primary',
                        'school',
                        __('Equipment'),
                        route('school.equipment.index'),
                        60,
                        'equipment.view_any',
                        'school.equipment.*',
                        'Wrench'
                    );
                }
            } catch (\Exception $e) {
                // Silently handle menu registration errors
            }
        });
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $configPath = module_path($this->name, config('modules.paths.generator.config.path'));

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $config = str_replace($configPath.DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $config_key = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $config);
                    $segments = explode('.', $this->nameLower.'.'.$config_key);

                    // Remove duplicated adjacent segments
                    $normalized = [];
                    foreach ($segments as $segment) {
                        if (end($normalized) !== $segment) {
                            $normalized[] = $segment;
                        }
                    }

                    $key = ($config === 'config.php') ? $this->nameLower : implode('.', $normalized);

                    $this->publishes([$file->getPathname() => config_path($config)], 'config');
                    $this->merge_config_from($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Merge config from the given path recursively.
     */
    protected function merge_config_from(string $path, string $key): void
    {
        $existing = config($key, []);
        $module_config = require $path;

        config([$key => array_replace_recursive($existing, $module_config)]);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        Blade::componentNamespace(config('modules.namespace').'\\' . $this->name . '\\View\\Components', $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }
}
