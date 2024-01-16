<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\UserProject;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        return Inertia::render('Home/Index');
    }

    public function dashboard()
    {
        $user = Auth::guard('user')->user();
        $totalProjects = UserProject::where('user_id', $user->id)->count();
        return Inertia::render('User/Index', [
            'totalProjects' => $totalProjects
        ]);
    }
}
