<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        $users = User::latest()->paginate(10);
        
        return view('Admin.users.index', compact('users'));
    }
}