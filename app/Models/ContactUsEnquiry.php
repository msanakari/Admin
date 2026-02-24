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

class ContactUsEnquiry extends Model
{
    protected $table   = 'contactenquiry';
    public $timestamps = true;

}
