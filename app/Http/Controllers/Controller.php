<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function functionResponse($data, $responseCode = 200)
    {
        return response()->json($data, $responseCode);
    }
       
    public function apiResponse($data, $responseCode = 200)
    {
        return response()->json($data, $responseCode);
    }
}
