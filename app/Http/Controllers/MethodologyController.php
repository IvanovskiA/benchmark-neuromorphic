<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class MethodologyController extends Controller
{
    public function index(): View
    {
        return view('methodology.index');
    }
}
