<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $path = 'admin.dashboard.';

    public function index()
    {
        $data = [
            'title' => 'Dashboard',
        ];
        
        return view($this->path . 'index', $data);
    }
}
