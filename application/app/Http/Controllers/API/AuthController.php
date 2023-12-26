<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    /**
     * Login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6 ',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $user = User::where('email', $request->email)->first();

        if (empty($user) || !Hash::check($request->password, $user->password)) {
            return response(['message' => 'Invalid credentials'], 422);
        }
        //
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];
        $response = $this->getAccessAndRefreshToken($credentials);
        return response($response, 200);
    }

    /**
     * Return the url of the google auth.
     * FE should call this and then direct to this url.
     */
    public function getAuthUrl()
    {
        try {
            $url = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
        } catch (\Exception $exception) {
            return $exception;
        }
        return response($url, 200);
    }

    /**
     * Handle Google callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleAccount = Socialite::driver('google')->stateless()->user();
            $user = User::where('google_id', $googleAccount->id)->first();
            if (!$user) {
                $user = User::create([
                    'name' => $googleAccount->name,
                    'email' => $googleAccount->email,
                    'password' => Hash::make('password'),
                    'google_id'=> $googleAccount->id
                ]);
                $user->markEmailAsVerified();
            }
            // $token = $user->createToken('Bearer Token')->accessToken;
            $credentials = [
                'email' => $googleAccount->email,
                'password' => 'password'
            ];
            $response = $this->getAccessAndRefreshToken($credentials);
            return response($response, 200);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get access and refresh token
     * @param array $credentials
     */
    private function getAccessAndRefreshToken($credentials)
    {
        try {
            $response = Http::asForm()->post(env('OAUTH_TOKEN_URL'), [
                'grant_type' => 'password',
                'username' => $credentials['email'],
                'password' => $credentials['password'],
                'client_id' => 2,
                'client_secret' => 'tEBcBV0e0M6vrnIHVHXGk1hgItHmfNKXEPiTRkh5',
                'scope' => '*',
            ]);

            if ($response->getStatusCode() == 200) {
                $result = json_decode($response->getBody()->getContents(), true);
            }
            return $result ?? [];
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }
}
