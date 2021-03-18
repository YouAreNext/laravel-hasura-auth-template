<?php

namespace App\Support\Facades;

use App\Services\HasuraService;
use Illuminate\Support\Facades\Facade;

/**
 * @see App\Services\HasuraService
 */
class Hasura extends Facade
{
  /**
   * Get the registered name of the component.
   *
   * @return string
   */
  protected static function getFacadeAccessor()
  {
    return HasuraService::class;
  }
}
