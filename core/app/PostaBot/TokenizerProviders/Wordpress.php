<?php

namespace App\PostaBot\TokenizerProviders;

use App\PostaBot\Contracts\Tokenizable;
use Laravel\Socialite\Facades\Socialite;

class Wordpress implements Tokenizable
{


    public function __construct()
    {
        $this->client_id = config('services.wordpress.client_id');
        $this->client_key= config('services.wordpress.client_key');
        //dd($this->client_id);
        $this->client_secret = config('services.wordpress.client_secret');
        $this->redirect_uri = route('socialaccounts.connect.callback', 'wordpress');
       
    }



    public function redirect()
    {
       // return Socialite::driver('wordpress')->redirect();
        return Socialite::with('wordpress')->scopes(['user.info.basic', 'video.upload','video.list'])->redirect();
    }

    public function getAndSaveData()
    {
        $user = Socialite::driver('wordpress')->user();
        auth()->user()->accounts()->updateOrCreate(
            ['platform' => 'wordpress', 'uid' => $user->id],
            ['name' => $user->name, 'type' => 'Account', 'token' => $user->token, 'secret' => $user->tokenSecret]
        );
    }

    public function revoke($accessToken)
    {
    }
}
