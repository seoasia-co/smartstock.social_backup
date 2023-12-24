<?php

namespace App\PostaBot\TokenizerProviders;

use App\PostaBot\Contracts\Tokenizable;
use Laravel\Socialite\Facades\Socialite;

class Shopify implements Tokenizable
{


    public function __construct()
    {
        $this->client_id = config('services.shopify.client_id');
        //dd($this->client_id);
        $this->client_secret = config('services.shopify.client_secret');
        $this->redirect_uri = route('socialaccounts.connect.callback', 'shopify');
       
    }



    public function redirect()
    {
       //return Socialite::driver('shopify')->redirect();
       
       return Socialite::with('shopify')->scopes(['read_content', 'write_content'])->redirect();
    }

    public function getAndSaveData()
    {
        $user = Socialite::driver('shopify')->user();
        auth()->user()->accounts()->updateOrCreate(
            ['platform' => 'shopify', 'uid' => $user->id],
            ['name' => $user->name, 'type' => 'Account', 'token' => $user->token, 'secret' => $user->tokenSecret]
        );
    }
   
    public function revoke($access_token)
    {
       
    }
}
