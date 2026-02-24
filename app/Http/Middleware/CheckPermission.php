<?php

namespace App\Http\Middleware;

use Closure;

class CheckPermission
{
    protected $helper;

    public function __construct($helper)
    {
        $this->helper = $helper;
    }
    
    public function handle($request, Closure $next, $permissions)
    {
        if ($this->helper->has_permission(\Auth::guard('admin')->user()->id, $permissions)) {
            return $next($request);
        } else {
            abort(403, 'Access denied');
        }
    }
}
