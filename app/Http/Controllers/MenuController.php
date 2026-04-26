<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index1()
    {
        return view('buku');
    }
    public function index2()
    {
        return view('transaksi');
    }
    public function index3()
    {
        return view('peminjam');
    }
}
