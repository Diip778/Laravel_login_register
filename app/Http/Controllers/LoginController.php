<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except([
            'logout', 'dashboard'
        ]);
    }
    public function register(){
        return view('auth.register');
    }
    public function store(Request $request){
        $request->validate([
         'name'=>'required|string|max:100',
         'email'=>'required|email|max:20|unique:users',
         'password'=>'required|min:5|confirmed',
    ]);
    User::create([
        'name'=> $request->name,
        'email'=>$request->email,
        'password'=>Hash::make($request->password)
    ]);
    $credentials=$request->only('email','password');
    Auth::attempt($credentials);
    $request->session()->regenerate();
    return redirect()->route('dashboard')->withSuccess('You have Successfully registered & logged in');
    }
    
    public function login()
    {
     return view('auth.login');
    }
    public function authenticate(Request $request)
    {
        $credentials=$request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);
        if(Auth::attempt($credentials))
        {
            $request->session()->regenerate();
            return redirect()->route('dashboard')->withSuccess('You have logged in');
        }
        return back()->withErrors([
            'email'=>'Invalid Credentials',
        ])->onlyInput('email');
    }
    public function dashboard(){
        if(Auth::check())
        {
            return view('auth.dashboard');
        }
        return redirect()->route('login')->withErrors([
            'email'=>'Login First!',
        ])->onlyInput('email');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->withSuccess('Successfully Logged Out!');
    }
}
