<?php

namespace App\PostaBot\TokenizerProviders;

use App\PostaBot\Contracts\Tokenizable;
use Laravel\Socialite\Facades\Socialite;

class Tumblr implements Tokenizable
{


    public function __construct()
    {
        $this->client_id = config('services.tumblr.client_id');
        $this->client_key= config('services.tumblr.client_key');
        //dd($this->client_id);
        $this->client_secret = config('services.tumblr.client_secret');
        $this->redirect_uri = route('socialaccounts.connect.callback', 'tumblr');
        $this->id_laravel = auth()->user()->id;
       
    }



    public function redirect()
    {
        return Socialite::driver('tumblr')->redirect();
        //return Socialite::with('tumblr')->scopes(['user.info.basic', 'video.upload','video.list'])->redirect();
    }

    public function getAndSaveData()
    {
        
        $user = Socialite::driver('tumblr')->user();
        auth()->user()->accounts()->updateOrCreate(
            ['platform' => 'tumblr', 'uid' =>  trim($user->nickname)."_".$this->id_laravel],
            ['name' => $user->nickname, 'type' => 'Account', 'token' => $user->token, 'secret' => $user->tokenSecret]
        );
    }

    public function revoke($accessToken)
    {
    }
}
