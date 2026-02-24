<?php

/**
 * Country Model
 *
 * Country Model manages Country operation.
 *
 * @category   Language
 *
 * @deprecated None
 */


namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Cache;

class Properties extends Model
{
  protected $table   = 'properties';
    public $timestamps = true;

    public function images() {
           return $this->hasMany(PropertyImages::class, 'property_id', 'id');

    }

}
