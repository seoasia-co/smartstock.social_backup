<?php

namespace App\PostaBot;

use App\PostaBot\PublisherProviders\Facebook;
use App\PostaBot\PublisherProviders\Twitter;
use App\PostaBot\PublisherProviders\Tiktok;
use Exception;
use Illuminate\Support\Manager;

class PublisherManager extends Manager
{

    public function getDefaultDriver():Exception
    {
        throw new Exception('invalid driver');
    }

    public function createFacebookDriver():Facebook
    {
        return new Facebook();
    }

    public function createTwitterDriver():Twitter
    {
        return new Twitter();
    }

    public function createTiktokDriver():Tiktok
    {
        return new Tiktok();
    }

}
