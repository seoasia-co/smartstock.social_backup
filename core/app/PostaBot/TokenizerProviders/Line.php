<?php

namespace App\PostaBot\TokenizerProviders;

use App\PostaBot\Contracts\Tokenizable;
use Laravel\Socialite\Facades\Socialite;

class Line implements Tokenizable
{


    public function __construct()
    {
        $this->client_id = config('services.line.client_id');
        //dd($this->client_id);
        $this->client_secret = config('services.line.client_secret');
        $this->redirect_uri = route('socialaccounts.connect.callback', 'line');
       
    }



    public function redirect()
    {
        return Socialite::driver('line')->redirect();
        //return Socialite::with('line')->scopes(['profile', 'profile%20openid'])->redirect();
    }

    public function getAndSaveData()
    {
        $user = Socialite::driver('line')->user();
        auth()->user()->accounts()->updateOrCreate(
            ['platform' => 'line', 'uid' => $user->id],
            ['name' => $user->name, 'type' => 'Account', 'token' => $user->token, 'secret' => $user->tokenSecret]
        );
    }

    public function revoke($accessToken)
    {
    }
}
