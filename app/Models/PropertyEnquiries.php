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

class PropertyEnquiries extends Model
{
  protected $table   = 'propertyenquiries';
    public $timestamps = true;

   public function property() {
        return $this->belongsTo(Properties::class);
    }

}
