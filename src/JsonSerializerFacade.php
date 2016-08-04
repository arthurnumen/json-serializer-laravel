<?php 

namespace Arthurnumen;

use Illuminate\Support\Facades\Facade;

class JsonSerializerFacade extends Facade {
  protected static function getFacadeAccessor() {
    return 'jsonserializer';
  }
}