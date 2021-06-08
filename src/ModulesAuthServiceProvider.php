<?php

namespace Modullo\ModulesAuth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;

class ModulesAuthServiceProvider extends ServiceProvider {

  public function boot(Filesystem $filesystem)
  {
    $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    $this->loadViewsFrom(__DIR__.'/resources/views', 'modules-auth');
    $this->publishes([
      __DIR__.'/config/modules-auth.php' => config_path('modules-auth.php'),
    ], 'config');
    $this->publishes([
      __DIR__.'/config/modules-auth-form-fields.php' => config_path('modules-auth-form-fields.php'),
    ], 'config');
    $this->publishes([
      __DIR__.'/assets' => public_path('vendor/modules-auth')
    ], 'modules-auth');
    if (!class_exists('CreateAuthUsersTable')){
        $this->publishes([
            __DIR__ . '/../database/migrations/create_auth_users_table.php.stub' => $this->getMigrationFileName($filesystem,'create_auth_users_table')], 'migrations');
    }

  }

  public function register()
  {
  }

    protected function getMigrationFileName(Filesystem $filesystem, string $table_name): string
    {
        $timestamp = date('Y_m_d_His');

        return Collection::make($this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem,$table_name) {
                return $filesystem->glob($path.'*'.$table_name.'.php');
            })->push($this->app->databasePath()."/migrations/{$timestamp}_$table_name.php")
            ->first();
    }

}


?>