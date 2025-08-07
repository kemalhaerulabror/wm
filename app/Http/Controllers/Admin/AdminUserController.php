<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;

class AdminUserController extends Controller
{
    /**
     * Menampilkan daftar semua admin.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $admins = Admin::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.admin-user.index', compact('admins'));
    }

    /**
     * Menampilkan detail admin.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $admin = Admin::findOrFail($id);
        return view('admin.admin-user.show', compact('admin'));
    }
} 