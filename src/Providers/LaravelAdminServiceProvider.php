<?php

namespace Joselfonseca\LaravelAdmin\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class LaravelAdminServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    protected $providers = [
        'Kodeine\Acl\AclServiceProvider',
        'Collective\Html\HtmlServiceProvider',
    ];
    protected $aliases = [
        'Form' => 'Collective\Html\FormFacade',
        'Html' => 'Collective\Html\HtmlFacade',
    ];

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands()
             ->registerOtherProviders()
             ->registerAliases();
    }

    public function boot()
    {
        $this->loadViewsConfiguration()
             ->loadRoutes()
             ->publishesConfiguration()
             ->publishesAssets()
             ->registerTranslations();
    }

    private function registerOtherProviders()
    {
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
        return $this;
    }

    protected function registerAliases()
    {
        foreach ($this->aliases as $alias => $original) {
            AliasLoader::getInstance()->alias($alias, $original);
        }
        return $this;
    }

    private function loadViewsConfiguration()
    {
        $this->loadViewsFrom(__DIR__ . '/../Views/', 'LaravelAdmin');
        $this->publishes([
            __DIR__ . '/../Views/' => base_path('resources/views/vendor/LaravelAdmin'),
        ]);
        return $this;
    }

    private function publishesConfiguration()
    {
        $this->publishes([
            __DIR__ . '/../config/laravel-admin.php' => config_path('laravel-admin.php'),
        ], 'LAconfig');
        return $this;
    }

    private function publishesAssets()
    {
        $this->publishes([
            __DIR__ . '/../../public' => public_path('vendor/laravelAdmin'),
        ], 'public');
        return $this;
    }

    private function loadRoutes()
    {
        include __DIR__ . '/../Http/routes.php';
        return $this;
    }

    private function registerCommands()
    {
        $this->app->bind('command.laravel-admin.install', 'Joselfonseca\LaravelAdmin\Console\Installer');
        $this->commands('command.laravel-admin.install');
        return $this;
    }

    private function registerTranslations(){
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'LaravelAdmin');
        $this->publishes([
            __DIR__ . '/../../resources/lang' => base_path('resources/lang'),
        ], 'LALang');
        return $this;
    }

}