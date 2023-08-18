<?php

namespace RachidLaasri\LaravelInstaller\Helpers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EnvironmentManager
{
    /**
     * @var string
     */
    private $envPath;

    /**
     * @var string
     */
    private $envExamplePath;

    /**
     * Set the .env and .env.example paths.
     */
    public function __construct()
    {
        $this->envPath = base_path('.env');
        $this->envExamplePath = base_path('.env.example');
    }

    /**
     * Get the content of the .env file.
     *
     * @return string
     */
    public function getEnvContent()
    {
        if (! file_exists($this->envPath)) {
            if (file_exists($this->envExamplePath)) {
                copy($this->envExamplePath, $this->envPath);
            } else {
                touch($this->envPath);
            }
        }

        return file_get_contents($this->envPath);
    }

    /**
     * Get the the .env file path.
     *
     * @return string
     */
    public function getEnvPath()
    {
        return $this->envPath;
    }

    /**
     * Get the the .env.example file path.
     *
     * @return string
     */
    public function getEnvExamplePath()
    {
        return $this->envExamplePath;
    }

    /**
     * Save the edited content to the .env file.
     *
     * @param Request $input
     * @return string
     */
    public function saveFileClassic(Request $input)
    {
        $message = trans('installer_messages.environment.success');

        try {
            file_put_contents($this->envPath, $input->get('envConfig'));
        } catch (Exception $e) {
            $message = trans('installer_messages.environment.errors');
        }

        return $message;
    }

    /**
     * Save the form content to the .env file.
     *
     * @param Request $request
     * @return string
     */
    public function saveFileWizard(Request $request)
    {
        $results = trans('installer_messages.environment.success');

        $envFileData =
            "APP_NAME='" . $request->app_name . "'\n" .
            'APP_ENV=' . $request->environment . "\n" .
            'APP_KEY=' . 'base64:jZ6n9I2WTTrBB59H5rHMF3HBKU4Doj60hm4T8v0W1+8=' . "\n" .
            'APP_DEBUG=' . $request->app_debug . "\n" .
            'APP_LOG_LEVEL=' . $request->app_log_level . "\n" .
            'APP_URL=' . $request->app_url . "\n\n" .
            'LOG_CHANNEL=stack'."\n\n" .
            'LOG_DEPRECATIONS_CHANNEL=null'."\n\n" .
            'LOG_LEVEL=debug'."\n\n" .
            'DB_CONNECTION=' . $request->database_connection . "\n" .
            'DB_HOST=' . $request->database_hostname . "\n" .
            'DB_PORT=' . $request->database_port . "\n" .
            'DB_DATABASE=' . $request->database_name . "\n" .
            'DB_USERNAME=' . $request->database_username . "\n" .
            "DB_PASSWORD='" . $request->database_password . "'\n\n" .
            'DB_TABLE_PREFIX=smartend_' . "\n\n" .
            'BROADCAST_DRIVER=' . $request->broadcast_driver . "\n" .
            'CACHE_DRIVER=' . $request->cache_driver . "\n" .
            'FILESYSTEM_DISK=local'. "\n" .
            'QUEUE_CONNECTION=sync'. "\n" .
            'SESSION_DRIVER=file' . "\n" .
            'SESSION_DRIVER=file' . "\n" .
            'SESSION_LIFETIME=120' . "\n" .
            'MEMCACHED_HOST=127.0.0.1' . "\n" .
            'REDIS_HOST=' . $request->redis_hostname . "\n" .
            'REDIS_PASSWORD=' . $request->redis_password . "\n" .
            'REDIS_PORT=' . $request->redis_port . "\n\n" .
            'MAIL_DRIVER=' . $request->mail_driver . "\n" .
            'MAIL_HOST=' . $request->mail_host . "\n" .
            'MAIL_PORT=' . $request->mail_port . "\n" .
            'MAIL_USERNAME=' . $request->mail_username . "\n" .
            'MAIL_PASSWORD=' . $request->mail_password . "\n" .
            'MAIL_ENCRYPTION=' . $request->mail_encryption . "\n\n" .
            'MAIL_FROM_ADDRESS=noreplay@site.com' . "\n\n" .
            'MAIL_FROM_NAME="${APP_NAME}"' . "\n\n" .
            'AWS_ACCESS_KEY_ID=' . "\n\n" .
            'AWS_SECRET_ACCESS_KEY=' . "\n\n" .
            'AWS_DEFAULT_REGION=' . "\n\n" .
            'AWS_BUCKET=' . "\n\n" .
            'AWS_USE_PATH_STYLE_ENDPOINT=false' . "\n\n" .
            'PUSHER_APP_ID=' . $request->pusher_app_id . "\n" .
            'PUSHER_APP_KEY=' . $request->pusher_app_key . "\n" .
            'PUSHER_APP_SECRET=' . $request->pusher_app_secret  . "\n" .
            'PUSHER_HOST='."\n".
            'PUSHER_PORT=443'."\n".
            'PUSHER_SCHEME=https'."\n".
            'PUSHER_APP_CLUSTER=mt1'."\n".
            'VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"'."\n".
            'VITE_PUSHER_HOST="${PUSHER_HOST}"'."\n".
            'VITE_PUSHER_PORT="${PUSHER_PORT}"'."\n".
            'VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"'."\n".
            'VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"'."\n".
            'NOCAPTCHA_STATUS=0'."\n".
            'NOCAPTCHA_SECRET='."\n".
            'NOCAPTCHA_SITEKEY='."\n".
            'BACKEND_PATH=admin'."\n".
            'BACKEND_PAGINATION=30'."\n".
            'FRONTEND_PAGINATION=20'."\n".
            'FRONTEND_TOPICS_ORDER=asc'."\n".
            'DEFAULT_LANGUAGE=en'."\n".
            'NO_REPLAY_EMAIL=noreplay@site.com'."\n".
            'FACEBOOK_STATUS=0'."\n".
            'FACEBOOK_ID='."\n".
            'FACEBOOK_SECRET='."\n".
            'TWITTER_STATUS=0'."\n".
            'TWITTER_ID='."\n".
            'TWITTER_SECRET='."\n".
            'GOOGLE_STATUS=0'."\n".
            'GOOGLE_ID='."\n".
            'GOOGLE_SECRET='."\n".
            'LINKEDIN_STATUS=0'."\n".
            'LINKEDIN_ID='."\n".
            'LINKEDIN_SECRET='."\n".
            'GITHUB_STATUS=0'."\n".
            'GITHUB_ID='."\n".
            'GITHUB_SECRET='."\n".
            'BITBUCKET_STATUS=0'."\n".
            'BITBUCKET_ID='."\n".
            'BITBUCKET_SECRET='."\n".
            'TIMEZONE=UTC'."\n".
            'GOOGLE_MAPS_KEY='."\n".
            'GEOIP_STATUS=1'."\n".
            'GEOIP_SERVICE=ipapi'."\n".
            'GEOIP_SERVICE_KEY='."\n".
            'FIRST_DAY_OF_WEEK=0'."\n".
            'DATE_FORMAT=Y-m-d'."\n".
            'RSS_STATUS=1'."\n";

        try {
            file_put_contents($this->envPath, $envFileData);
        } catch (Exception $e) {
            $results = trans('installer_messages.environment.errors');
        }

        return $results;
    }
}
