<?php

namespace App\PostaBot\TokenizerProviders;

use App\PostaBot\Contracts\Tokenizable;
use Laravel\Socialite\Facades\Socialite;

class Shopify implements Tokenizable
{

    public function generateRandomString($length = 11) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }



    public function __construct()
    {
        $this->client_id = config('services.shopify.client_id');
        $this->client_key= config('services.shopify.client_key');
        //dd($this->client_id);
        $this->client_secret = config('services.shopify.client_secret');
        $this->redirect_uri = route('socialaccounts.connect.callback', 'shopify');
        $csrfState = $this->generateRandomString();
       
    }



    public function redirect()
    {
        //return Socialite::driver('shopify')->redirect();
        //return Socialite::with('shopify')->scopes(['read_content', 'write_content'])->redirect();
        //return Socialite::driver('shopify')->scopes(['read_content', 'write_content'])->redirect();

        $url = 'https://{shop}.myshopify.com/admin/oauth/authorize';

        $url.= "?client_key={$this->client_key}";
        //$url.= "&client_secret={$this->client_secret}";
        $url.= "&scope=read_content,write_content";
        $url.= "&redirect_uri={$this->redirect_uri}";
        $url.= "&grant_options[]=online";
        $url.= "&state=".$this->generateRandomString();
        return redirect()->to($url);

    
    }


    public function getAndSaveData()
    {
        $user = Socialite::driver('shopify')->user();
        auth()->user()->accounts()->updateOrCreate(
            ['platform' => 'shopify', 'uid' => $user->id],
            ['name' => $user->name, 'type' => 'Account', 'token' => $user->token, 'secret' => $user->tokenSecret]
        );
    }

    public function revoke($accessToken)
    {
    }
}
