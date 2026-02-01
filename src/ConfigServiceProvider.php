<?php

declare(strict_types=1);

namespace WPZylos\Framework\Config;

use WPZylos\Framework\Core\Contracts\ApplicationInterface;
use WPZylos\Framework\Core\ServiceProvider;

/**
 * Configuration service provider.
 *
 * Loads .env and config files during registration.
 *
 * @package WPZylos\Framework\Config
 */
class ConfigServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function register(ApplicationInterface $app): void
    {
        parent::register($app);

        // Register config repository
        $this->singleton(ConfigRepository::class, function () use ($app) {
            $config = new ConfigRepository();

            // Load .env first (optional, no error if missing)
            $envPath   = $app->paths()->path('.env');
            $envLoader = new EnvLoader();
            $envLoader->load($envPath);

            // Load config directory
            $configPath = $app->paths()->path('@config');
            $config->loadDirectory($configPath);

            return $config;
        });

        // Alias
        $this->bind('config', fn() => $this->make(ConfigRepository::class));
    }
}
