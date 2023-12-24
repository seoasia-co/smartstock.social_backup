<?php

namespace App\PostaBot\TokenizerProviders;

use App\PostaBot\Contracts\Tokenizable;
use Laravel\Socialite\Facades\Socialite;

class Tiktok implements Tokenizable
{


    public function __construct()
    {
        $this->client_id = config('services.tiktok.client_id');
        //$this->client_key= config('services.tiktok.client_key');
        //dd($this->client_id);
        $this->client_secret = config('services.tiktok.client_secret');
        $this->redirect_uri = route('socialaccounts.connect.callback', 'tiktok');
       
    }



    public function redirect()
    {
       //return Socialite::driver('tiktok')->redirect();
        return Socialite::with('tiktok')->scopes(['user.info.basic', 'video.upload','video.list'])->redirect();
    }

    public function getAndSaveData()
    {
        $user = Socialite::driver('tiktok')->user();
        auth()->user()->accounts()->updateOrCreate(
            ['platform' => 'tiktok', 'uid' => $user->id],
            ['name' => $user->name, 'type' => 'Account', 'token' => $user->token, 'secret' => $user->tokenSecret]
        );
    }
   
    public function revoke($access_token)
    {
        $response = Http::delete("https://open-api.tiktok.com/oauth/revoke/?access_token={$access_token}");
        if ($response->ok()) {
            return true;
        }

        return false;
    }
}
