<?php

namespace App\PostaBot\TokenizerProviders;

use App\PostaBot\Contracts\Tokenizable;
use Laravel\Socialite\Facades\Socialite;

class Telegram implements Tokenizable
{


    public function __construct()
    {
        $this->client_id = config('services.telegram.client_id');
        //$this->client_key= config('services.telegram.client_key');
        //dd($this->client_id);
        $this->client_secret = config('services.telegram.client_secret');
        $this->redirect_uri = route('socialaccounts.connect.callback', 'telegram');
       
    }



    public function redirect()
    {
       // return Socialite::driver('telegram')->redirect();
        return Socialite::with('telegram')->scopes(['user.info.basic', 'video.upload','video.list'])->redirect();
    }

    public function getAndSaveData()
    {
        $user = Socialite::driver('telegram')->user();
        auth()->user()->accounts()->updateOrCreate(
            ['platform' => 'telegram', 'uid' => $user->id],
            ['name' => $user->name, 'type' => 'Account', 'token' => $user->token, 'secret' => $user->tokenSecret]
        );
    }

    public function revoke($accessToken)
    {
    }
}
