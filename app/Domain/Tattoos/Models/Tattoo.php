<?php

namespace App\Domain\Tattoos\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Tattoos\Models\TattooTag;
use App\Domain\Tattoos\Models\TattooLike;
use App\Domain\Tattoos\Models\TattooImage;
use App\Domain\Tattoos\Models\TattooWatch;
use App\Domain\Tattoos\Models\TattooComment;

class Tattoo extends Model
{
  const CREATOR_TYPE_USER = 1,
        CREATOR_TYPE_STUDIO = 2;

  public function images()
  {
    return $this->hasMany(TattooImage::class, 'tattoo_id', 'id');
  }

  public function saves()
  {
    return $this->hasMany(TattooSave::class, 'tattoo_id', 'id');
  }

  public function likes()
  {
    return $this->hasMany(TattooLike::class, 'tattoo_id', 'id');
  }

  public function watches()
  {
    return $this->hasMany(TattooWatch::class, 'tattoo_id', 'id');
  }

  public function tags()
  {
    return $this->hasMany(TattooTag::class, 'tattoo_id', 'id')
                ->with('tag');
  }

  public function comments()
  {
    return $this->hasMany(TattooComment::class, 'tattoo_id', 'id')
                ->with('user');
  }
}