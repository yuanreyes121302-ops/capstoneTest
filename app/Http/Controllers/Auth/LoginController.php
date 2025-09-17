<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected function redirectTo()
{
    if (auth()->user()->role === 'admin') {
        return '/admin/users/all';
    } elseif (auth()->user()->role === 'landlord') {
        return '/landlord/dashboard';
    } elseif (auth()->user()->role === 'tenant') {
        return '/tenant/profile';
    }

    return '/'; // default fallback
}


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    protected function authenticated(\Illuminate\Http\Request $request, $user)
    {
        if (!$user->is_approved && $user->role !== 'tenant' && $user->role !== 'admin') {
            Auth::logout();
            return redirect('/login')->withErrors([
                'email' => 'Your account is awaiting admin approval.',
            ]);
        }
        return redirect()->intended($this->redirectPath());
    }

}
