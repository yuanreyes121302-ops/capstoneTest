<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'user_id' => ['required', 'string', 'max:255', 'unique:users'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'gender' => ['required', 'in:male,female'],
            'role' => ['required', 'in:tenant,landlord'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        // Add contact_number validation for landlords
        if (isset($data['role']) && $data['role'] === 'landlord') {
            $rules['contact_number'] = ['required', 'string', 'max:20'];
        }

        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $userData = [
            'user_id' => $data['user_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'gender' => $data['gender'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
            'is_approved' => $data['role'] === 'tenant',
        ];

        // Add contact_number for landlords
        if ($data['role'] === 'landlord' && isset($data['contact_number'])) {
            $userData['contact_number'] = $data['contact_number'];
        }

        return User::create($userData);
    }

    protected function registered(Request $request, $user)
    {
        if ($user->role === 'tenant') {
            return redirect()->route('tenant.properties.index');
        } elseif ($user->role === 'landlord') {
            return redirect()->route('landlord.properties.index');
        } elseif ($user->role === 'admin') {
            return redirect()->route('admin.users.all');
        }
        return redirect('/');
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        // Don't log the user in automatically
        $message = $request->role === 'tenant'
            ? 'Registration successful. You can now log in.'
            : 'Registration successful. Please wait for admin approval.';

        return redirect('/login')->with('success', $message);
    }
}
