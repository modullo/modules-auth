<?php

namespace Modullo\ModulesAuth;
use Illuminate\Support\ServiceProvider;

class ModulesAuthServiceProvider extends ServiceProvider {

  public function boot()
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
    $this->publishes([
      __DIR__ . '/../database/migrations/create_auth_users_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time
        ()) . '_create_auth_users_table.php'),
      // you can add any number of migrations here
    ], 'migrations');
  }

  public function register()
  {
//    //add menu config
//    $this->mergeConfigFrom(
//      __DIR__.'/config/navigation-menu.php', 'navigation-menu.modules-auth.sub-menu'
//    );
  }

}


?>