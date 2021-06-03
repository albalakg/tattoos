<?php

namespace App\Domain\Tattoos\Controllers;

use App\Domain\Tattoos\Models\Tattoo;
use App\Domain\Tattoos\Services\TattooService;
use App\Http\Controllers\Controller;

class TattooController extends Controller
{
  public function __construct()
  {
    $this->service = new TattooService;
  }

   
}