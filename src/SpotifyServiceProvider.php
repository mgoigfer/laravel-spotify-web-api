<?php

namespace Mgoigfer\Spotify;

use Illuminate\Support\ServiceProvider;
use Mgoigfer\Spotify\Spotify;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/spotify.php' => config_path('spotify.php'),
            ], 'config');

            if (!class_exists('CreateSpotifyTable')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../database/migrations/create_spotify_table.php.stub' => database_path('migrations/'.$timestamp.'_create_spotify_table.php'),
                ], 'migrations');
            }
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->singleton('Spotify', function ($app, $parameters)
        {
            $session = new Session(
                config('spotify.client_id'),
                config('spotify.client_secret'),
                config('app.url').$parameters['callback']
            );

            $api = new SpotifyWebAPI();

            return new Spotify($session, $api, $parameters);
        });
    }
}
