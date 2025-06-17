# Shutdown Scheduler

[![License: GPL-3.0](https://img.shields.io/badge/License-GPL%203.0-blue.svg)](https://opensource.org/licenses/GPL-3.0)

A lightweight PHP library for managing shutdown events in your applications. Shutdown Scheduler allows you to register callbacks that will execute when your script ends, ensuring proper cleanup and resource handling even during unexpected script termination.

## 📋 Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Basic Usage](#basic-usage)
- [Advanced Examples](#advanced-examples)
- [Cron Job Implementation](#cron-job-implementation)
- [API Reference](#api-reference)
- [Development](#development)
- [Running Tests](#running-tests)
- [Coding Standards](#coding-standards)
- [Contributing](#contributing)
- [License](#license)
- [Basic Usage](#basic-usage)
- [Advanced Examples](#advanced-examples)
- [Cron Job Implementation](#cron-job-implementation)
- [API Reference](#api-reference)
- [Contributing](#contributing)
- [License](#license)

## ✨ Features

- **Reliable Execution**: Callbacks are guaranteed to run when the script terminates
- **Event-Based System**: Register multiple shutdown events with unique names
- **Simple API**: Intuitive methods for registering and unregistering callbacks
- **Error Handling**: Built-in validation for callbacks and event names
- **Resource Cleanup**: Perfect for database connections, file handlers, and temporary resources

## 🚀 Installation

### Manual Installation

Simply include the `ShutdownScheduler.php` file in your project:

```php
require_once 'path/to/ShutdownScheduler.php';
```

### Composer (Recommended)

Add the package to your `composer.json` file:

```json
{
    "require": {
        "abhishekdas/shutdown-scheduler": "^1.0.0"
    }
}
```

Then run:

```
composer install
```

## 🔰 Basic Usage

Here's a simple example of how to use Shutdown Scheduler:

```php
<?php
// If installed via Composer
require_once 'vendor/autoload.php';

// If included manually
// require_once 'ShutdownScheduler.php';

// Import the namespace
use AbhishekDas\ShutdownScheduler\ShutdownScheduler;

// Create a new instance
$shutdownScheduler = new ShutdownScheduler();

// Register a shutdown event with a callback function
$shutdownScheduler->registerShutdownEvent('cleanup', function() {
    echo "Cleaning up resources...\n";
    // Close database connections, file handlers, etc.
});

// Your application code here...
echo "Application running...\n";

// If needed, you can unregister an event
// $shutdownScheduler->unregisterShutdownEvent('cleanup');

// When the script ends, the 'cleanup' function will be executed automatically
?>
```

## 🔄 Advanced Examples

### Cron Job Implementation

The following example demonstrates how to use the ShutdownScheduler in a cron job to prevent multiple instances from running simultaneously:

```php
<?php
require_once 'vendor/autoload.php';

use AbhishekDas\ShutdownScheduler\ShutdownScheduler;

class Cron
{
    public function run()
    {
        // Check if the cron job is already running
        if ($this->isCronRunning()) {
            return false; // Avoid duplicate execution
        }

        // Set the cron job as running
        $this->setCronRunning();

        // Create a new ShutdownScheduler instance
        $shutdownScheduler = new ShutdownScheduler();

        // Register a shutdown event to clear the running status
        $eventName = 'clearRunning-' . time();
        $shutdownScheduler->registerShutdownEvent($eventName, [$this, 'clearCronRunning']);

        try {
            // Run your cron job logic here
            // ...
            
            // Set the cron job as successful
            $this->setCronSuccess();
        } finally {
            // Unregister the shutdown event if everything completes normally
            $shutdownScheduler->unregisterShutdownEvent($eventName);
        }
        
        return true;
    }

    // Implementation methods would go here
    // ...
}
```

## 📘 API Reference

### `AbhishekDas\ShutdownScheduler\ShutdownScheduler` Class

#### Constructor

```php
public function __construct()
```

Initializes the ShutdownScheduler and registers the internal shutdown function.

#### Methods

##### `registerShutdownEvent(string $eventName, callable $callback, mixed ...$params)`

Registers a shutdown event with a callback function.

- **Parameters:**
  - `$eventName` (string): A unique name for this shutdown event
  - `$callback` (callable): The function to be called on shutdown
  - `...$params` (mixed): Optional parameters to pass to the callback

- **Returns:** `bool` - `true` on success, `false` on failure

##### `unregisterShutdownEvent(string $eventName)`

Unregisters a previously registered shutdown event.

- **Parameters:**
  - `$eventName` (string): The name of the event to unregister

##### `callRegisteredShutdown()`

Internal method that executes all registered callbacks when the script terminates.

## � Development

### Running Tests

This project uses PHPUnit for testing. To run the tests:

1. Install development dependencies:

```bash
composer install --dev
```

2. Run the test suite:

```bash
vendor/bin/phpunit
```

### Coding Standards

We follow the PSR-12 coding standard. To check your code against these standards:

```bash
vendor/bin/phpcs
```

To automatically fix some coding standard issues:

```bash
vendor/bin/phpcbf
```

## �👥 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the GNU General Public License v3.0 - see the [LICENSE](LICENSE) file for details.

---

Made with ❤️ by [Abhishek Das](https://stackoverflow.com/users/1634267/jumbo)
