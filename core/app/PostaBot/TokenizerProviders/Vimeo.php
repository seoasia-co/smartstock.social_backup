<?php

namespace App\PostaBot\TokenizerProviders;

use App\PostaBot\Contracts\Tokenizable;
use Laravel\Socialite\Facades\Socialite;

class Vimeo implements Tokenizable
{


    public function __construct()
    {
        $this->client_id = config('services.vimeo.client_id');
      
        //dd($this->client_id);
        $this->client_secret = config('services.vimeo.client_secret');
        $this->redirect_uri = route('socialaccounts.connect.callback', 'vimeo');
       
    }



    public function redirect()
    {
        //return Socialite::driver('vimeo')->redirect();
        return Socialite::with('vimeo')->scopes(['public*', 'upload','create','edit','stat'])->redirect();
    }

    public function getAndSaveData()
    {
        $user = Socialite::driver('vimeo')->user();
        auth()->user()->accounts()->updateOrCreate(
            ['platform' => 'vimeo', 'uid' => $user->id],
            ['name' => $user->name, 'type' => 'Account', 'token' => $user->token, 'secret' => $user->tokenSecret]
        );
    }

    public function revoke($accessToken)
    {
    }
}
