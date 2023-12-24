<?php

namespace App\PostaBot\TokenizerProviders;

use App\PostaBot\Contracts\Tokenizable;
use Laravel\Socialite\Facades\Socialite;

class Wechat implements Tokenizable
{


    public function __construct()
    {
        $this->client_id = config('services.wechat.client_id');
        $this->client_key= config('services.wechat.client_key');
        //dd($this->client_id);
        $this->client_secret = config('services.wechat.client_secret');
        $this->redirect_uri = route('socialaccounts.connect.callback', 'wechat');
       
    }



    public function redirect()
    {
       // return Socialite::driver('wechat')->redirect();
        return Socialite::with('wechat')->scopes(['snsapi_userinfo', 'broadcast'])->redirect();
    }

    public function getAndSaveData()
    {
        $user = Socialite::driver('wechat')->user();
        auth()->user()->accounts()->updateOrCreate(
            ['platform' => 'wechat', 'uid' => $user->id],
            ['name' => $user->name, 'type' => 'Account', 'token' => $user->token, 'secret' => $user->tokenSecret]
        );
    }

    public function revoke($accessToken)
    {
    }
}
