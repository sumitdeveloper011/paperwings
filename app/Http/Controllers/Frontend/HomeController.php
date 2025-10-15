<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $viewPath = 'frontend.home.';
    
    public function index(){
        $title = 'Home';
        return view($this->viewPath . 'index', compact('title'));
    }
}
