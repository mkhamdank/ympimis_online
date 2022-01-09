<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RioController extends Controller
{
    function indexrio(){
       
       return view('ibat.index')->with('page', 'Rio');
    }
}
