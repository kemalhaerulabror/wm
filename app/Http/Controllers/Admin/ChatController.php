<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Menampilkan halaman chat admin
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.chat.index');
    }
}
