<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_ID'),
        'client_secret' => env('FACEBOOK_SECRET'),
        'redirect' => env('APP_URL') . '/oauth/facebook/callback',
    ],
    'twitter' => [
        'client_id' => env('TWITTER_ID'),
        'client_secret' => env('TWITTER_SECRET'),
        'redirect' => env('APP_URL') . '/oauth/twitter/callback',
    ],
    'google' => [
        'client_id' => env('GOOGLE_ID'),
        'client_secret' => env('GOOGLE_SECRET'),
        'redirect' => env('APP_URL') . '/oauth/google/callback',
    ],
    'linkedin' => [
        'client_id' => env('LINKEDIN_ID'),
        'client_secret' => env('LINKEDIN_SECRET'),
        'redirect' => env('APP_URL') . '/oauth/linkedin/callback',
    ],
    'github' => [
        'client_id' => env('GITHUB_ID'),
        'client_secret' => env('GITHUB_SECRET'),
        'redirect' => env('APP_URL') . '/oauth/github/callback',
    ],
    'bitbucket' => [
        'client_id' => env('BITBUCKET_ID'),
        'client_secret' => env('BITBUCKET_SECRET'),
        'redirect' => env('APP_URL') . '/oauth/bitbucket/callback',
    ],
    'firebase' => [
        'project_id' => env('FIREBASE_PROJECT_ID'), 
        'private_key_id' => env('FIREBASE_PRIVATE_KEY_ID'), 
        'private_key' => env('FIREBASE_PRIVATE_KEY'),
        'client_email' => env('FIREBASE_CLIENT_EMAIL'), 
        'client_id' => env('FIREBASE_CLIENT_ID'), 
        'auth_uri' => env('FIREBASE_AUTH_URI'), 
        'token_uri' => env('FIREBASE_TOKEN_URI'), 
        'auth_provider_x509_cert_url' => env('FIREBASE_AUTH_PROVIDER_X509_CERT_URL'), 
        'client_x509_cert_url' => env('FIREBASE_CLIENT_X509_CERT_URL')
    ],
];
