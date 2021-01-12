<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Services\CompanieService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

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
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('check.if.session.has.plan');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'min:3', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'max:16', 'confirmed'],
            'companie'  => ['required', 'string', 'min:3', 'max:255', 'unique:companies,name'],
            'cnpj'  => ['required', 'numeric', 'digits:14', 'unique:companies'],
        ]);
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {

      if(!session()->has('plan')) {
          return redirect()
                 ->route('site.home')
                 ->with('warning', 'Selecione um plano antes de se registrar!');
      }

      $plan = session('plan');

      $companieService = app(CompanieService::class);

      $user = $companieService->make($plan, $data);
      return $user;
    }
}
