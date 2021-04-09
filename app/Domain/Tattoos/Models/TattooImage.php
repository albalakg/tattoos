<?php

namespace App\Domain\Tattoos\Models;

use App\Domain\Tattoos\Models\Tattoo;
use Illuminate\Database\Eloquent\Model;

class TattooImage extends Model
{
  public function tattoo()
  {
    return $this->hasOne(Tattoo::class)
                ->select('id', 'title', 'image');
  }
}