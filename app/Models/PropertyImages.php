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

class PropertyImages extends Model
{
  protected $table   = 'propertyimages';
    // public $timestamps = true;

 public function property() {
        return $this->belongsTo(Properties::class);
    }

}
