<?php

namespace App\PostaBot\TokenizerProviders;

use App\PostaBot\Contracts\Tokenizable;
use Laravel\Socialite\Facades\Socialite;

class Pinterest implements Tokenizable
{


    public function __construct()
    {
        $this->client_id = config('services.pinterest.client_id');
        
        //dd($this->client_id);
        $this->client_secret = config('services.pinterest.client_secret');
        $this->redirect_uri = route('socialaccounts.connect.callback', 'pinterest');
       
    }



    public function redirect()
    {
       // return Socialite::driver('pinterest')->redirect();
        return Socialite::with('pinterest')->scopes(['user_accounts:read', 'pins:read','pins:write'])->redirect();
    }

    public function getAndSaveData()
    {
        $user = Socialite::driver('pinterest')->user();
        auth()->user()->accounts()->updateOrCreate(
            ['platform' => 'pinterest', 'uid' => $user->id],
            ['name' => $user->name, 'type' => 'Account', 'token' => $user->token, 'secret' => $user->tokenSecret]
        );
    }

    public function revoke($accessToken)
    {
    }
}
