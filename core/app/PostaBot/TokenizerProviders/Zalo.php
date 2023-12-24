<?php

namespace App\PostaBot\TokenizerProviders;

use App\PostaBot\Contracts\Tokenizable;
use Laravel\Socialite\Facades\Socialite;

class Zalo implements Tokenizable
{


    public function __construct()
    {
        $this->client_id = config('services.zalo.client_id');
        $this->client_key= config('services.zalo.client_key');
        //dd($this->client_id);
        $this->client_secret = config('services.zalo.client_secret');
        $this->redirect_uri = route('socialaccounts.connect.callback', 'zalo');
       
    }



    public function redirect()
    {
       // return Socialite::driver('zalo')->redirect();
        return Socialite::with('zalo')->scopes(['user.info.basic', 'video.upload','video.list'])->redirect();
    }

    public function getAndSaveData()
    {
        $user = Socialite::driver('zalo')->user();
        auth()->user()->accounts()->updateOrCreate(
            ['platform' => 'zalo', 'uid' => $user->id],
            ['name' => $user->name, 'type' => 'Account', 'token' => $user->token, 'secret' => $user->tokenSecret]
        );
    }

    public function revoke($accessToken)
    {
    }
}
