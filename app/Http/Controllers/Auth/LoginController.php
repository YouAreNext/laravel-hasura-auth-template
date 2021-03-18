<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Support\Facades\Hasura;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LoginController extends Controller
{
  /**
   * Instantiate a new LoginController instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('auth:jwt', ['except' => 'login']);
  }

  /**
   * Get auth user
   *
   * @return \Illuminate\Http\Response
   */
  public function me()
  {
    // return response(User::findOrFail(Auth::id())->only(['id']));
    $user = User::findOrFail(Auth::id());

    return response()->json([
      "user_id" => (string) $user->id
    ]);
  }

  /**
   * Get a JWT via given credentials.
   * 
   * @return \Illuminate\Http\Response
   */
  public function login(Request $request)
  {
    $this->validateLogin($request);

    $credentials = $request->input;

    if (!$token = $this->attempt($credentials)) {
      throw new BadRequestHttpException('Invalid credentials.');
    }

    return response(['access_token' => $token]);
  }

  /**
   * Validate the user login request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return void
   *
   * @throws \Illuminate\Validation\ValidationException
   */
  protected function validateLogin(Request $request)
  {
    $this->validate($request, [
      'input.email' => 'required|email',
      'input.password' => 'required|string',
    ]);
  }

  /**
   * Attempt to log the user.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return bool
   */
  protected function attempt($credentials)
  {
    $user = User::firstWhere('email', $credentials['email']);
    if ($user) {
      if (Hash::check($credentials['password'], $user->getAuthPassword())) {
        return Hasura::jwt('App\Models\User', $user->id, $user->role);
      }
    }
  }
}
