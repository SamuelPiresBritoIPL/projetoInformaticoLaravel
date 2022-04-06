<?php

namespace App\Http\Controllers;

use App\Models\Anoletivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Adldap\Laravel\Facades\Adldap;

class AuthController extends Controller
{
    public function login(Request $request){
        if (Auth::attempt($request->only(['login', 'password']))) {
            
            // Returns \App\User model configured in `config/auth.php`.
            //$user = Adldap::search()->users()->find('2191727');
            $user = Auth::user();
            
            dd($user);
        }
        
        dd("deu merda");
    }
}
