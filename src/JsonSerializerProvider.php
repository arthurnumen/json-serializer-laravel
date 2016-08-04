<?php 

namespace Arthurnumen;

use Illuminate\Support\ServiceProvider;
use League\Fractal\Manager;

class JsonSerializerProvider extends ServiceProvider {

  /**
   * Indicates if loading of the provider is deferred.
   *
   * @var bool
   */
  protected $defer = false;

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {
    $this->app->singleton('jsonserializer', function ($app) {
      $includes = $app['request']->input('include');
      
      $manager = new Manager;

      if ($includes) {
        $manager->parseIncludes($includes);
      }
      
      return new JsonSerializer($manager);
    });
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides()
  {
    return array('jsonserializer');
  }

}
