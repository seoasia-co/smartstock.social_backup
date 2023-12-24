<?php

namespace App\PostaBot\TokenizerProviders;

use App\PostaBot\Contracts\Tokenizable;
use Laravel\Socialite\Facades\Socialite;

class Flickr implements Tokenizable
{


    public function __construct()
    {
        $this->client_id = config('services.flickr.client_id');
        //dd($this->client_id);
        $this->client_secret = config('services.flickr.client_secret');
        $this->redirect_uri = route('socialaccounts.connect.callback', 'flickr');
       
    }



    public function redirect()
    {
       return Socialite::driver('flickr')->redirect();
       //return Socialite::driver('flickr')->redirect();
       // return Socialite::with('flickr')->perms(['read', 'write','delete'])->redirect();
        //return Socialite::driver('flickr')->perms(['read', 'write','delete'])->redirect();
        //return Socialite::driver('flickr')->scopes(['read', 'write','delete'])->redirect();
    }

    public function getAndSaveData()
    {
        $user = Socialite::driver('flickr')->user();
        auth()->user()->accounts()->updateOrCreate(
            ['platform' => 'flickr', 'uid' => $user->id],
            ['name' => $user->name, 'type' => 'Account', 'token' => $user->token, 'secret' => $user->tokenSecret]
        );
    }

    public function revoke($accessToken)
    {
    }
}
