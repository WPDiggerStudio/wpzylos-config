# WPZylos Config

[![PHP Version](https://img.shields.io/badge/php-%5E8.0-blue)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![GitHub](https://img.shields.io/badge/GitHub-WPDiggerStudio-181717?logo=github)](https://github.com/WPDiggerStudio/wpzylos-config)

Configuration management with dot-notation and .env support for WPZylos framework.

📖 **[Full Documentation](https://wpzylos.com)** | 🐛 **[Report Issues](https://github.com/WPDiggerStudio/wpzylos-config/issues)**

---

## ✨ Features

- **Dot-notation Access** — Access nested values with `config('app.name')`
- **Environment Support** — Load from `.env` files
- **Caching** — Cache compiled configuration
- **Type Safety** — Typed getters with defaults
- **Merging** — Deep merge configuration arrays

---

## 📋 Requirements

| Requirement | Version |
| ----------- | ------- |
| PHP         | ^8.0    |

---

## 🚀 Installation

```bash
composer require wpdiggerstudio/wpzylos-config
```

---

## 📖 Quick Start

```php
use WPZylos\Framework\Config\Repository;

$config = new Repository([
    'app' => [
        'name' => 'My Plugin',
        'debug' => true,
    ],
    'database' => [
        'prefix' => 'myplugin_',
    ],
]);

// Dot-notation access
$config->get('app.name');           // 'My Plugin'
$config->get('app.debug');          // true
$config->get('missing', 'default'); // 'default'
```

---

## 🏗️ Core Features

### Dot-notation Access

```php
$config->get('database.connections.mysql.host');
$config->set('database.connections.mysql.port', 3307);
$config->has('database.connections.mysql');
```

### Loading from Files

```php
$config = Repository::load(__DIR__ . '/config');

// Loads all PHP files in config/ directory
// config/app.php -> config('app.key')
// config/database.php -> config('database.key')
```

### Environment Variables

```php
// In .env
APP_DEBUG=true
DB_HOST=localhost

// In config/app.php
return [
    'debug' => env('APP_DEBUG', false),
    'db_host' => env('DB_HOST', 'localhost'),
];
```

---

## 📦 Related Packages

| Package                                                                  | Description            |
| ------------------------------------------------------------------------ | ---------------------- |
| [wpzylos-core](https://github.com/WPDiggerStudio/wpzylos-core)           | Application foundation |
| [wpzylos-container](https://github.com/WPDiggerStudio/wpzylos-container) | Dependency injection   |
| [wpzylos-scaffold](https://github.com/WPDiggerStudio/wpzylos-scaffold)   | Plugin template        |

---

## 📖 Documentation

For comprehensive documentation, tutorials, and API reference, visit **[wpzylos.com](https://wpzylos.com)**.

---

## ☕ Support the Project

If you find this package helpful, consider buying me a coffee! Your support helps maintain and improve the WPZylos ecosystem.

<a href="https://www.paypal.com/donate/?hosted_button_id=66U4L3HG4TLCC" target="_blank">
  <img src="https://img.shields.io/badge/Donate-PayPal-blue.svg?style=for-the-badge&logo=paypal" alt="Donate with PayPal" />
</a>

---

## 📄 License

MIT License. See [LICENSE](LICENSE) for details.

---

## 🤝 Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

---

**Made with ❤️ by [WPDiggerStudio](https://github.com/WPDiggerStudio)**
