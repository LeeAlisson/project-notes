<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function loginSubmit(Request $request)
    {
        // form validation
        $request->validate(
            [
                'text_username' => 'required|email',
                'text_password' => 'required|min:4|max:16'
            ], 
            [
                'text_username.required' => 'O username é obrigatório',
                'text_username.email' => 'O username precisa ser um e-mail válido',
                'text_password.required' => 'A password é obrigatória',
                'text_password.min' => 'A password deve ter no mínimo :min caracteres',
                'text_password.max' => 'A password deve ter no máximo :max caracteres'
            ]
        );

        // get user input
        $username = $request->input('text_username');
        $password = $request->input('text_password');

        // check if users exists
        $user = User::where('username', $username)->where('deleted_at', NULL)->first();

        if(!$user){
            return redirect()->back()->withInput()->with('loginError', 'Username incorreto.');
        }

        // check if password is correct
        if(!password_verify($password, $user->password)){
            return redirect()->back()->withInput()->with('loginError', 'Password incorreto.');
        }

        // update last login
        $user->last_login = date('Y-m-d H:i:s');
        $user->save();

        // login user
        session([
            'user' => [
                'id' => $user->id,
                'username' => $user->username
            ]
        ]);

        return redirect()->to('/');
    }

    public function logout()
    {
        // logout from the application
        session()->forget('user');
        
        return redirect()->to('/login');
    }
}
