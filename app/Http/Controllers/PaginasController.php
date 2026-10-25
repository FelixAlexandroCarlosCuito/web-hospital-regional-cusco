<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaginasController extends Controller
{
    public function inicio()
    {
        return view('inicio');
    }

    public function reservar_cita(){
        return view('citas.reservar_cita');
    }
}