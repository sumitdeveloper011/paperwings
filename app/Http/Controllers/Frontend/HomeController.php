<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $viewPath = 'frontend.home.';
    
    public function index(){
        $title = 'Home';
        $sliders = Slider::active()->ordered()->get();
        $categories = Category::active()->ordered()->take(6)->get();
        return view($this->viewPath . 'index', compact('title', 'sliders', 'categories'));
    }
}
