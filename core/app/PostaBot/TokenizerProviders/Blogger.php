<?php

namespace App\PostaBot\TokenizerProviders;

use App\PostaBot\Contracts\Tokenizable;
use Laravel\Socialite\Facades\Socialite;

class Blogger implements Tokenizable
{
    public function redirect()
    {
        return Socialite::driver('blogger')->redirect();
    }

    public function getAndSaveData()
    {
        $user = Socialite::driver('blogger')->user();
        auth()->user()->accounts()->updateOrCreate(
            ['platform' => 'blogger', 'uid' => $user->id],
            ['name' => $user->nickname, 'type' => 'Account', 'token' => $user->token, 'secret' => $user->tokenSecret]
        );
    }

    public function revoke($accessToken)
    {
    }
}
