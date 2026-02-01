<?php

declare(strict_types=1);

namespace WPZylos\Framework\Config;

use WPZylos\Framework\Core\Support\Arr;

/**
 * Configuration repository.
 *
 * Stores configuration values with dot-notation access.
 *
 * @package WPZylos\Framework\Config
 */
class ConfigRepository
{
    /**
     * @var array<string, mixed> Configuration items
     */
    private array $items;

    /**
     * Create repository.
     *
     * @param array<string, mixed> $items Initial items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Get configuration value using dot notation.
     *
     * @param string $key Configuration key (e.g., 'app.debug')
     * @param mixed $default Default value if not found
     *
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->items, $key, $default);
    }

    /**
     * Set configuration value using dot notation.
     *
     * @param string $key Configuration key
     * @param mixed $value Value to set
     *
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        Arr::set($this->items, $key, $value);
    }

    /**
     * Check if a configuration key exists.
     *
     * @param string $key Configuration key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return Arr::has($this->items, $key);
    }

    /**
     * Get all configuration items.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Get typed string value.
     *
     * @param string $key Configuration key
     * @param string $default Default value
     *
     * @return string
     */
    public function string(string $key, string $default = ''): string
    {
        $value = $this->get($key, $default);

        return is_string($value) ? $value : (string) $value;
    }

    /**
     * Get typed integer value.
     *
     * @param string $key Configuration key
     * @param int $default Default value
     *
     * @return int
     */
    public function int(string $key, int $default = 0): int
    {
        return (int) $this->get($key, $default);
    }

    /**
     * Get typed float value.
     *
     * @param string $key Configuration key
     * @param float $default Default value
     *
     * @return float
     */
    public function float(string $key, float $default = 0.0): float
    {
        return (float) $this->get($key, $default);
    }

    /**
     * Get typed boolean value.
     *
     * @param string $key Configuration key
     * @param bool $default Default value
     *
     * @return bool
     */
    public function bool(string $key, bool $default = false): bool
    {
        $value = $this->get($key, $default);

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return in_array(strtolower($value), ['true', '1', 'yes', 'on'], true);
        }

        return (bool) $value;
    }

    /**
     * Get typed array value.
     *
     * @param string $key Configuration key
     * @param array $default Default value
     *
     * @return array
     */
    public function array(string $key, array $default = []): array
    {
        $value = $this->get($key, $default);

        return is_array($value) ? $value : $default;
    }

    /**
     * Load configuration files from a directory.
     *
     * Each PHP file should return an array.
     * Filename (without extension) becomes the top-level key.
     *
     * @param string $path Directory path
     *
     * @return void
     */
    public function loadDirectory(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        $files = glob($path . '/*.php');

        if ($files === false) {
            return;
        }

        foreach ($files as $file) {
            $key = basename($file, '.php');
            $config = require $file;

            if (is_array($config)) {
                $this->items[$key] = $config;
            }
        }
    }

    /**
     * Merge configuration items.
     *
     * @param array<string, mixed> $items Items to merge
     *
     * @return void
     */
    public function merge(array $items): void
    {
        $this->items = array_replace_recursive($this->items, $items);
    }
}
