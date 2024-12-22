<?php

namespace Lara\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;

use Gate;

use Illuminate\Validation\Rule;
use Lara\User;
use Lara\Person;
use Lara\Section;
use Lara\Status;

use Lara\Http\Controllers\Controller;
use Lara\Utilities;

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
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
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
            'name' => 'required|max:255',
            'givenname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'section' => [
                'required',
                Rule::in(
                    Section::all()->map(
                        function(Section $section) { return $section->id;}
                    )->toArray()
                )
            ],
            'status' => ['required', Rule::in(Status::ACTIVE)]
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::createNew($data);
    }

    /**
     * Register the user with the data provided in the request.
     * We overwrite the register method from the RegisterUsers trait,
     * as we don't want to login the new User right away.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $this->authorize('create', User::class);

        if (Gate::denies('createUserOfSection', $request->input('section'))) {
            Utilities::error('You cannot create a user of another section!');
            return redirect($this->redirectPath());
        }

        event(new Registered($user = $this->create($request->all())));

        return redirect($this->redirectPath());
    }
}
