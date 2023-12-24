<?php

namespace App\PostaBot\TokenizerProviders;

use App\PostaBot\Contracts\Tokenizable;
use Laravel\Socialite\Facades\Socialite;

class Youtube implements Tokenizable
{


    public function __construct()
    {
        $this->client_id = config('services.youtube.client_id');
        
        //dd($this->client_id);
        $this->client_secret = config('services.youtube.client_secret');
        $this->redirect_uri = route('socialaccounts.connect.callback', 'youtube');
       
    }



    public function redirect()
    {
       // return Socialite::driver('youtube')->redirect();
        return Socialite::with('youtube')->scopes(['https://www.googleapis.com/auth/userinfo.profile','https://www.googleapis.com/auth/youtube.upload'])->redirect();
    }

    public function getAndSaveData()
    {
        $user = Socialite::driver('youtube')->user();
        auth()->user()->accounts()->updateOrCreate(
            ['platform' => 'youtube', 'uid' => $user->id],
            ['name' => $user->nickname, 'type' => 'Account', 'token' => $user->token, 'secret' => $user->tokenSecret]
        );
    }

    public function revoke($accessToken)
    {
    }
}
