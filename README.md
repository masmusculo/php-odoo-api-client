PHP Odoo API Client (JSON-RPC)
===============================

[![Latest Stable Version](https://poser.pugx.org/ang3/php-odoo-api-client/v/stable)](https://packagist.org/packages/ang3/php-odoo-api-client) 
[![Total Downloads](https://poser.pugx.org/ang3/php-odoo-api-client/downloads)](https://packagist.org/packages/ang3/php-odoo-api-client)

## 🔄 Fork Information

This is a fork of the original [ang3/php-odoo-api-client](https://github.com/Ang3/php-odoo-api-client) project, maintained to provide full JSON-RPC support for both **PHP 7** and **PHP 8+**.

The fork was created to complete the JSON-RPC implementation that was pending in the original 8.x version, allowing this library to be used in projects that still require PHP 7.

## ✨ Features

- ✅ Complete Odoo API client via JSON-RPC
- ✅ Compatible with PHP 7.1+ and PHP 8.0+
- ✅ No dependency on PHP's `xmlrpc` extension
- ✅ Support for all Odoo operations (CRUD, searches, etc.)
- ✅ Automatic authentication
- ✅ Robust error handling
- ✅ Compatible with [official Odoo documentation](https://www.odoo.com/documentation/13.0/developer/misc/api/odoo.html)

## 📌 Branches and Versioning

This project maintains two main branches:

| Branch | PHP Version | Nomenclature | Example |
|--------|-------------|--------------|---------|
| `master` | PHP 8.0+ | `8.8.x.y` | `8.8.1.0` |
| `master-php7` | PHP 7.1+ | `8.7.x.y` | `8.7.1.0` |

### Versioning System

Versioning follows the format `A.B.x.y`:

- **A** (8): Inherited from the original official library
- **B** (8 or 7): Indicates the supported PHP version
  - `8` = PHP 8.0+
  - `7` = PHP 7.1+
- **x.y**: Fork-specific versioning (major.minor)

## 🔌 Odoo Compatibility

| Odoo Version | Compatibility | Comment |
|--------------|---------------|---------|
| v17.0        | ✅ Yes        | Tested  |
| v16.0        | ✅ Yes        | Tested  |
| v15.0        | ✅ Yes        | Tested  |
| v14.0        | ✅ Yes        | Tested  |
| v13.0        | ✅ Yes        | Tested  |
| v12.0        | ✅ Yes        | Tested  |
| Older        | ⚠️ Likely     | Untested |

## 📦 Installation

### For PHP 8.0+

```bash
composer require osunasport/php-odoo-api-client:8.8.*
```

### For PHP 7.1+

```bash
composer require osunasport/php-odoo-api-client:8.7.*
```

### Installation from Git Repository

If you need to use the version directly from the repository:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/YOUR_USERNAME/php-odoo-api-client"
        }
    ],
    "require": {
        "osunasport/php-odoo-api-client": "8.8.*"
    }
}
```

> **Note:** Replace `YOUR_USERNAME` with the user/organization where your fork is hosted.

## 🚀 Basic Usage

### Creating a Client

You can create a client using DSN or array configuration.

#### Option 1: Using DSN

The DSN must follow the format: `https://<username>:<password>@<host>/<database_name>`

```php
<?php

require_once 'vendor/autoload.php';

use Ang3\Component\Odoo\Client;

// If your password has special characters, encode it
$password = urlencode('my_special_password');

$dsn = "https://user@example.com:{$password}@odoo.example.com/my_database";
$client = Client::create($dsn);
```

#### Option 2: Using Array Configuration

```php
<?php

require_once 'vendor/autoload.php';

use Ang3\Component\Odoo\Client;

$client = Client::createFromConfig([
    'host' => 'odoo.example.com',        // Without http:// or https://
    'database' => 'my_database',
    'username' => 'user@example.com',
    'password' => 'my_api_key_or_password',
    'scheme' => 'https',                 // Optional, defaults to 'https'
]);
```

### Basic Operations

#### Get Odoo Version

```php
$version = $client->version();
echo $version; // e.g., "15.0"
```

#### Execute Methods with executeKw

```php
// Search and read records
$partners = $client->executeKw(
    'res.partner',           // Model
    'search_read',           // Method
    [[['is_company', '=', true]]], // Search domain
    ['fields' => ['name', 'email', 'phone'], 'limit' => 10] // Options
);

foreach ($partners as $partner) {
    echo "Name: {$partner['name']}\n";
    echo "Email: {$partner['email']}\n";
}
```

## 📋 Complete Example: Querying Delivery Carriers

```php
<?php

require_once 'vendor/autoload.php';

use Ang3\Component\Odoo\Client;

// Configuration
$client = Client::createFromConfig([
    'host' => 'your-instance.odoo.com',
    'username' => 'your-username',
    'password' => 'your-api-key',
    'database' => 'your-database',
]);

try {
    // Get all delivery carriers
    $carriers = $client->executeKw(
        'delivery.carrier',
        'search_read',
        [],
        [
            'fields' => [
                'id', 'name', 'delivery_type', 'active', 
                'sequence', 'fixed_price', 'free_over', 
                'company_id', 'product_id'
            ]
        ]
    );

    echo "Total carriers: " . count($carriers) . "\n\n";

    foreach ($carriers as $carrier) {
        echo "ID: {$carrier['id']}\n";
        echo "Name: {$carrier['name']}\n";
        echo "Type: {$carrier['delivery_type']}\n";
        echo "Active: " . ($carrier['active'] ? 'Yes' : 'No') . "\n";
        echo "------------------------\n";
    }

    // Get only active carriers
    $activeCarriers = $client->executeKw(
        'delivery.carrier',
        'search_read',
        [[['active', '=', true]]],
        ['fields' => ['id', 'name']]
    );

    echo "\nActive carriers: " . count($activeCarriers) . "\n";

} catch (\Ang3\Component\Odoo\Exception\AuthenticationException $e) {
    echo "Authentication error: " . $e->getMessage() . "\n";
} catch (\Ang3\Component\Odoo\Exception\RequestException $e) {
    echo "Request error: " . $e->getMessage() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```

See the [06_consulta_carriers.php](examples/06_consulta_carriers.php) file for a complete example with HTML output.

## 🔍 Common Operations

### Searching Records

```php
// Simple search
$ids = $client->executeKw(
    'res.partner',
    'search',
    [[['is_company', '=', true]]]
);

// Search with limit and offset
$ids = $client->executeKw(
    'res.partner',
    'search',
    [[['customer_rank', '>', 0]]],
    ['limit' => 5, 'offset' => 10]
);
```

### Reading Records

```php
// Read specific records
$partners = $client->executeKw(
    'res.partner',
    'read',
    [[$id1, $id2, $id3]],
    ['fields' => ['name', 'email']]
);
```

### Creating Records

```php
$newId = $client->executeKw(
    'res.partner',
    'create',
    [[
        'name' => 'New Customer',
        'email' => 'customer@example.com',
        'phone' => '+1 555 123 4567',
        'is_company' => true
    ]]
);

echo "New record created with ID: $newId\n";
```

### Updating Records

```php
$client->executeKw(
    'res.partner',
    'write',
    [
        [$id],  // IDs to update
        [       // Values to update
            'phone' => '+1 555 987 6543',
            'mobile' => '+1 555 000 0000'
        ]
    ]
);
```

### Deleting Records

```php
$client->executeKw(
    'res.partner',
    'unlink',
    [[$id1, $id2]]
);
```

### Counting Records

```php
$count = $client->executeKw(
    'res.partner',
    'search_count',
    [[['is_company', '=', true]]]
);

echo "Total companies: $count\n";
```

## ⚠️ Exception Handling

The library throws different exceptions depending on the error type:

```php
use Ang3\Component\Odoo\Exception\AuthenticationException;
use Ang3\Component\Odoo\Exception\RequestException;
use Ang3\Component\Odoo\Exception\TransportException;

try {
    $result = $client->executeKw('res.partner', 'search_read', []);
} catch (AuthenticationException $e) {
    // Authentication error (incorrect credentials)
    echo "Authentication error: " . $e->getMessage();
} catch (RequestException $e) {
    // Request error (model doesn't exist, method not allowed, etc.)
    echo "Request error: " . $e->getMessage();
} catch (TransportException $e) {
    // Transport error (connection, timeout, etc.)
    echo "Transport error: " . $e->getMessage();
}
```

## 🔧 Custom Transport

By default, the library uses JSON-RPC. If you need a custom transport:

```php
use Ang3\Component\Odoo\Transport\TransportInterface;

class MyCustomTransport implements TransportInterface
{
    // Implement required methods
}

$transport = new MyCustomTransport();
$client = Client::create($dsn, $transport);
```

## 🌐 Additional Features

### DBAL (Database Abstraction Layer)

For advanced database abstraction features, check out the complementary package:
- [PHP Odoo DBAL](https://github.com/ang3/php-odoo-dbal) (for original version only)

## 🤝 Contributing

Contributions are welcome. Please:

1. Fork the project
2. Create a feature branch (`git checkout -b feature/MyNewFeature`)
3. Commit your changes (`git commit -am 'Add new feature'`)
4. Push to the branch (`git push origin feature/MyNewFeature`)
5. Open a Pull Request

## 🐛 Reporting Issues

If you find a bug or have a suggestion, please open an issue on GitHub.

## 📝 Changelog

See the [CHANGELOG.md](CHANGELOG.md) file for the change history.

## 📄 License

This software is published under the [MIT License](./LICENCE).

## 🙏 Credits

- **Original Project**: [ang3/php-odoo-api-client](https://github.com/Ang3/php-odoo-api-client) by [Joanis ROUANET](https://github.com/Ang3)
- **Fork Maintained by**: OsunaSport / MasMusculo

## 📚 Useful Resources

- [Official Odoo API Documentation](https://www.odoo.com/documentation/17.0/developer/reference/backend/orm.html)
- [Odoo External API Guide](https://www.odoo.com/documentation/17.0/developer/misc/api/odoo.html)
- [Most Common Odoo Models](https://www.odoo.com/documentation/17.0/developer/reference/backend/models.html)

---

**Note**: This README is shared between the `master` (PHP 8) and `master-php7` (PHP 7) branches. Make sure to install the correct version according to your PHP environment.
