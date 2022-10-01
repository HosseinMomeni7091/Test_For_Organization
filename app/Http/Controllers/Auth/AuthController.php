<?php

namespace App\Http\Controllers\Auth;


use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        auth()->attempt($request->only("email","password"));
        // session(["name" => auth()->user()->name]);

        if (auth()->user()->role == "admin"){
            return view('adminfirstpage')->with("message","Welcome Dear Admin");
        }
        if (auth()->user()->role == "buyer"){
            $files=User::find(auth()->user()->id)->files(Auth()->user())->get();
            $count=$files->count();
            // dd($files,$files->count());
            return view('buyerfirstpage')->with("message","Welcome Dear Buyer  ".auth()->user()->email)->with("files",$files)->with("count",$count);
        }
    }

    public function register(Request $request)
    {
        // dd($request->all());
        $res = User::create([
            "name" => $request->get('name'),
            "phone" => $request->get('phone'),
            "email" => $request->get('email'),
            "password" => Hash::make($request->get('password')),
          ]);
          
        auth()->attempt($request->only("email","password"));
        $files=User::find(auth()->user()->id)->files(Auth()->user())->get();
        $count=$files->count();
        return view('buyerfirstpage')->with("message","Welcome Dear Buyer  ".auth()->user()->email)->with("files",$files)->with("count",$count);

        // session(["name" => auth()->user()->name]);

        // dd(auth()->user());
      
    }


    public function logout(Request $request)
  {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route("home");
  }
}
