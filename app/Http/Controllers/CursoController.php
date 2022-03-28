<?php

namespace App\Http\Controllers;

use App\Models\curso;
use Illuminate\Http\Request;
use App\Http\Resources\CursoResource;

class CursoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	return response(CursoResource::collection(curso::all()),200);
    }

    public function getCursoComCadeiras()
    {
        CursoResource::$format = "detailed";
    	return response(CursoResource::collection(curso::all()),200);
    }
    
}
