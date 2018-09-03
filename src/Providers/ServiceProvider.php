<?php
namespace Airtable\Providers;

use Airtable\Managers\Airtable;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider {

  /**
   * Perform post-registration booting of services.
   *
   * @return void
   */
  public function boot()
  {
    $this->publishes([
      __DIR__.'/../config.php' => config_path('airtable.php'),
    ]);
  }

  /**
   * Register bindings in the container.
   *
   * @return void
   */
  public function register()
  {
    $this->mergeConfigFrom(
      __DIR__.'/../config.php', 'airtable'
    );

    $this->app->singleton('airtable', function() {
      return new Airtable($this->app);
    });
  }

}