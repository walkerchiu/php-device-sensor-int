<?php

namespace WalkerChiu\DeviceSensor;

use Illuminate\Support\ServiceProvider;

class DeviceSensorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
           __DIR__ .'/config/device-sensor.php' => config_path('wk-device-sensor.php'),
        ], 'config');

        // Publish migration files
        $from = __DIR__ .'/database/migrations/';
        $to   = database_path('migrations') .'/';
        $this->publishes([
            $from .'create_wk_device_sensor_table.php'
                => $to .date('Y_m_d_His', time()) .'_create_wk_device_sensor_table.php',
        ], 'migrations');

        $this->loadTranslationsFrom(__DIR__.'/translations', 'php-device-sensor');
        $this->publishes([
            __DIR__.'/translations' => resource_path('lang/vendor/php-device-sensor'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                config('wk-device-sensor.command.cleaner')
            ]);
        }

        config('wk-core.class.device-sensor.device')::observe(config('wk-core.class.device-sensor.deviceObserver'));
        config('wk-core.class.device-sensor.deviceLang')::observe(config('wk-core.class.device-sensor.deviceLangObserver'));
        config('wk-core.class.device-sensor.deviceRegister')::observe(config('wk-core.class.device-sensor.deviceRegisterObserver'));
        config('wk-core.class.device-sensor.deviceRegisterLang')::observe(config('wk-core.class.device-sensor.deviceRegisterLangObserver'));
        config('wk-core.class.device-sensor.deviceState')::observe(config('wk-core.class.device-sensor.deviceStateObserver'));
        config('wk-core.class.device-sensor.deviceStateLang')::observe(config('wk-core.class.device-sensor.deviceStateLangObserver'));

        config('wk-core.class.device-sensor.data')::observe(config('wk-core.class.device-sensor.dataObserver'));
    }

    /**
     * Merges user's and package's configs.
     *
     * @return void
     */
    private function mergeConfig()
    {
        if (!config()->has('wk-device')) {
            $this->mergeConfigFrom(
                __DIR__ .'/config/device-sensor.php', 'wk-device-sensor'
            );
        }

        $this->mergeConfigFrom(
            __DIR__ .'/config/device-sensor.php', 'device-sensor'
        );
    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param String  $path
     * @param String  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        if (
            !(
                $this->app instanceof CachesConfiguration
                && $this->app->configurationIsCached()
            )
        ) {
            $config = $this->app->make('config');
            $content = $config->get($key, []);

            $config->set($key, array_merge(
                require $path, $content
            ));
        }
    }
}
