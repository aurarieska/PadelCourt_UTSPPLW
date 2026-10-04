<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PelangganController extends Controller
{
    public function lapangan(): View
    {
        return view('pelanggan.lapangan');
    }
}
