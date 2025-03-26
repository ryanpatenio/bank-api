<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
   public function test(Request $request){
        return json_message(EXIT_SUCCESS,'ok');
   }
}
