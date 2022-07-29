<?php

namespace App\Http\Controllers;

use App\Models\Testtbl;
use Illuminate\Http\Request;

class TesttblController extends Controller
{
    //
    public function index()
    {
        //   
        $testtbl = Testtbl::Orderby('id'); 
        
       
        return view("testtbl.testtbl", ['testtbls' => $testtbl->paginate(10)] );
    }
}
