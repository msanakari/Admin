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

class Settings extends Model
{
    protected $table   = 'settings';
    public $timestamps = true;

}
