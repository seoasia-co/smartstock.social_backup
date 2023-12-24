<?php

namespace App\PostaBot\TokenizerProviders;

use App\PostaBot\Contracts\Tokenizable;
use App\PostaBot\Exceptions\TokenizerException;
use Illuminate\Support\Facades\Http;


class Tiktok implements Tokenizable
{

      /**
     * get tiktok app required configurations from laravel configs
     */

    
   // protected $client_key;
   // protected $client_id;


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
        
        //$this->client_key = 'awa8yfrpf3ism6ms';
       $this->client_key= config('services.tiktok.client_key');
       //dd( $this->client_key);
       // $this->client_id = '7228844921288886277';
        $this->client_id= config('services.tiktok.client_id');
        //dd($this->client_id);

        //$this->client_secret = 'ca82d3fc77822f9d07c2303c06cef224';
        $this->client_secret= config('services.tiktok.client_secret');
        $csrfState = $this->generateRandomString();
        $this->csrfState = $csrfState;
        $this->redirect_uri = route('socialaccounts.connect.callback', 'tiktok');
    }
    public function redirect()
    {
       // return Socialite::driver('tiktok')->redirect();
        $url = 'https://www.tiktok.com/auth/authorize/';

        $url.= "?client_key={$this->client_key}";
       //$url.= "&client_secret={$this->client_secret}";
        $url.= "&scope=user.info.basic,video.upload,video.list";
        $url.= "&response_type=code";
        $url.= "&redirect_uri={$this->redirect_uri}";
        $url.= "&state=".$this->csrfState;
        return redirect()->to($url);

        

    }
    
   
    //wait fix
    public function getAndSaveData()
    {
        $this->access_token = $this->handleCallback();
       /*  $user = Socialite::driver('tiktok')->user();
        auth()->user()->accounts()->updateOrCreate(
            ['platform' => 'Tiktok', 'uid' => $user->id],
            ['name' => $user->nickname, 'type' => 'Account', 'token' => $user->token, 'secret' => $user->tokenSecret]
        ); */
         $account_info = $this->getAccountInfo();
         $account = auth()->user()->accounts()->updateOrCreate(
             ['platform' => 'tiktok', 'uid' => $account_info['uid']],
             ['name' => $account_info['name'], 'type' => $account_info['type'], 'token' => $account_info['token'], 'secret' => $account_info['secret']]
         );
    }



    private function handleCallback()
    {
        $code = request()->code;

        if (empty($code)) {
            throw new TokenizerException("Malformed Request , Please try again .. ");
        }

        // Get access token
        $response = Http::post('https://open-api.tiktok.com/oauth/access_token/', [
            'client_id' => $this->client_id,
            'client_key' => $this->client_key,
            'client_secret' => $this->client_secret,
            'redirect_uri' => $this->redirect_uri,
            'grant_type' => 'authorization_code',
            'code' => $code,
        ]);
    
        if (!$response->successful()) {
            //dd($response->json());
            throw new TokenizerException(($e = $response->json()['data']['description']) ? $e : "Error , Please try again .. ");
        }

        return $response->json()['access_token'];
    }

    private function getAccountInfo()
    {
        $access_token = $this->access_token;
        //$response = Http::get("https://open.tiktokapis.com/v2/user/info/?fields=open_id,union_id,avatar_url&access_token={$access_token}");
        $response =Http::withHeaders([
            'Authorization' => 'Bearer ' . $access_token,
        ])->get("https://open.tiktokapis.com/v2/user/info/?fields=open_id,union_id,avatar_url,display_name");

        if (!$response->successful()) {
            throw new TokenizerException("Malformed Request , Please try again .. ");
        }

        $account = $response->json();

        return ['platform' => 'tiktok', 'uid' => $account['open_id'], 'name' => $account['display_name'] , 'type' => 'Account', 'token' => $access_token, 'secret' => null];
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
