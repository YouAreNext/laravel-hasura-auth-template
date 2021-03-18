<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;

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


  /**
   * Create a new user instance after a valid registration.
   *
   * @param  array  $request
   * @return User
   */
  public function create(Request $request)
  {
    $this->validateRegister($request);

    $data = $request->all();

    $data['password'] = Hash::make($data['password']);

    $user = User::create($data);


    return response()->json([
      'user_id' => $user->id,
      'access_token' => $this->jwt($user->fresh()),
    ]);
  }

  /**
   * Create a new token.
   *
   * @param  \App\User   $user
   * @return string
   */
  protected function jwt(User $user)
  {
    $payload = [
      'iss' => 'Hasura-JWT-Auth',
      'sub' => (string) $user->id,
      'model' => 'App\Models\User',
      'iat' => time(),
      'exp' => time() + 60 * 60 * 24 * 7,
      'https://hasura.io/jwt/claims' => [
        'x-hasura-user-id' => (string) $user->id,
        'x-hasura-default-role' => 'ADMIN',
        'x-hasura-allowed-roles' => [
          'ADMIN'
        ]
      ]
    ];

    return JWT::encode($payload, env('JWT_SECRET'));
  }

  /**
   * Validate the user login request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return void
   *
   * @throws \Illuminate\Validation\ValidationException
   */
  protected function validateRegister(Request $request)
  {
    $request->validate([
      'email' => 'required|unique:users,email|email|max:36',
      'name' => 'required|max:64',
      'password' => 'required|string|min:6',
    ]);
  }
}
