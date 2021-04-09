<?php

namespace App\Domain\Tattoos\Models;

use App\Domain\Users\Models\User;
use App\Domain\Tattoos\Models\Tattoo;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\DocBlock\Tag;

class TattooTag extends Model
{
  public function tag()
  {
    return $this->hasOne(Tag::class)
                ->select('id', 'name');
  }

  public function tattoo()
  {
    return $this->hasOne(Tattoo::class)
                ->select('id', 'title', 'image');
  }
}