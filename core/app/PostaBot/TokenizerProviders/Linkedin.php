<?php

namespace App\PostaBot\TokenizerProviders;

use App\PostaBot\Contracts\Tokenizable;
use Laravel\Socialite\Facades\Socialite;

class Linkedin implements Tokenizable
{


    public function __construct()
    {
        $this->client_id = config('services.linkedin.client_id');
        //dd($this->client_id);
        $this->client_secret = config('services.linkedin.client_secret');
        $this->redirect_uri = route('socialaccounts.connect.callback', 'linkedin');
       
    }



    public function redirect()
    {
       // return Socialite::driver('linkedin')->redirect();
        return Socialite::with('linkedin')->scopes(['w_member_social', 'r_liteprofile'])->redirect();
    }

    public function getAndSaveData()
    {
        $user = Socialite::driver('linkedin')->user();
        auth()->user()->accounts()->updateOrCreate(
            ['platform' => 'linkedin', 'uid' => $user->id],
            ['name' => $user->name, 'type' => 'Account', 'token' => $user->token, 'secret' => $user->tokenSecret]
        );
    }

    public function revoke($accessToken)
    {
    }
}
