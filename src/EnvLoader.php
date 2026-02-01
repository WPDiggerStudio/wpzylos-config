<?php

declare(strict_types=1);

namespace WPZylos\Framework\Config;

/**
 * Environment file loader.
 *
 * Parses .env files and optionally sets environment variables.
 * Handles comments, quoted values, and variable expansion.
 *
 * @package WPZylos\Framework\Config
 */
class EnvLoader
{
    /**
     * @var array<string, string> Parsed environment values
     */
    private array $values = [];

    /**
     * @var bool Whether to set $_ENV
     */
    private bool $setEnv;

    /**
     * @var bool Whether to call putenv()
     */
    private bool $setPutenv;

    /**
     * Create env loader.
     *
     * @param bool $setEnv Set values in $_ENV
     * @param bool $setPutenv Set values via putenv()
     */
    public function __construct(bool $setEnv = true, bool $setPutenv = false)
    {
        $this->setEnv    = $setEnv;
        $this->setPutenv = $setPutenv;
    }

    /**
     * Load environment file.
     *
     * Does NOT throw if a file is missing - optional in WP runtime.
     *
     * @param string $path Path to .env file
     *
     * @return bool True if loaded
     */
    public function load(string $path): bool
    {
        if (! is_file($path) || ! is_readable($path)) {
            return false;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            return false;
        }

        foreach ($lines as $line) {
            $this->parseLine($line);
        }

        return true;
    }

    /**
     * Parse a single line from the.env file.
     *
     * @param string $line Line content
     *
     * @return void
     */
    private function parseLine(string $line): void
    {
        $line = trim($line);

        // Skip comments and empty lines
        if ($line === '' || str_starts_with($line, '#')) {
            return;
        }

        // Skip lines without =
        if (! str_contains($line, '=')) {
            return;
        }

        // Split on first = only
        [ $name, $value ] = explode('=', $line, 2);

        $name  = trim($name);
        $value = $this->parseValue(trim($value));

        // Store value
        $this->values[ $name ] = $value;

        // Set in environment
        if ($this->setEnv) {
            $_ENV[ $name ] = $value;
        }

        if ($this->setPutenv) {
            putenv("{$name}={$value}");
        }
    }

    /**
     * Parse value, handling quotes and special values.
     *
     * @param string $value Raw value
     *
     * @return string Parsed value
     */
    private function parseValue(string $value): string
    {
        // Handle quoted values
        if (preg_match('/^"(.*)"\s*(#.*)?$/', $value, $matches)) {
            return $this->processEscapes($matches[1]);
        }

        if (preg_match("/^'(.*)'\s*(#.*)?$/", $value, $matches)) {
            // Single quotes: no escape processing
            return $matches[1];
        }

        // Unquoted: strip inline comments
        if (str_contains($value, ' #')) {
            $value = explode(' #', $value, 2)[0];
        }

        $value = trim($value);

        // Handle special values
        return match (strtolower($value)) {
            'true', '(true)' => 'true',
            'false', '(false)' => 'false',
            'null', '(null)', 'empty', '(empty)' => '',
            default => $value,
        };
    }

    /**
     * Process escape sequences in double-quoted values.
     *
     * @param string $value Value to process
     *
     * @return string Processed value
     */
    private function processEscapes(string $value): string
    {
        return str_replace(
            [ '\\n', '\\r', '\\t', '\\"', '\\\\' ],
            [ "\n", "\r", "\t", '"', '\\' ],
            $value
        );
    }

    /**
     * Get a parsed value.
     *
     * @param string $key Variable name
     * @param string|null $default Default value
     *
     * @return string|null
     */
    public function get(string $key, ?string $default = null): ?string
    {
        return $this->values[ $key ] ?? $default;
    }

    /**
     * Check if a variable exists.
     *
     * @param string $key Variable name
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->values[ $key ]);
    }

    /**
     * Get all parsed values.
     *
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->values;
    }

    /**
     * Get environment value from any source.
     *
     * Checks in order: loaded values, $_ENV, getenv().
     *
     * @param string $key Variable name
     * @param mixed $default Default value
     *
     * @return mixed
     */
    public static function env(string $key, mixed $default = null): mixed
    {
        // Check $_ENV first
        if (isset($_ENV[ $key ])) {
            return $_ENV[ $key ];
        }

        // Then getenv()
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        return $default;
    }
}
