# OmniRoute

**OmniRoute** is a lightweight, flexible PHP routing library designed to simplify the management of routes in your PHP applications. It allows you to define routes, handle URL parameters with regular expressions, and manage error callbacks for common HTTP response codes.

## Features

- **Route Registration**: Easily register routes with specific HTTP methods and callbacks.
- **Prefix Support**: Apply a prefix to a group of routes to organize and manage them efficiently.
- **Error Handling**: Register custom callbacks for handling `404 Not Found` and `405 Method Not Allowed` errors.
- **Regular Expression Arguments**: Support for dynamic URL parameters using regular expressions.
- **Sub-Router Integration**: Include and register routes from external files for better modularization.

## Installation

Include the OmniRoute class in your project by requiring the `Router.php` file and any necessary dependencies:

```php
require_once 'path/to/Router.php';
```

## Usage

### Basic Route Registration

You can register a simple route by calling the `add` method:

```php
use OmniRoute\Router;

Router::add('/home', function() {
    echo "Welcome to the homepage!";
}, ['GET']);
```

### Route with Parameters

To create a route with dynamic parameters, use placeholders wrapped in `<: :>`:

```php
Router::add('/user/<:id:>', function($id) {
    echo "User ID: " . $id;
}, ['GET']);
```

### Prefixing Routes

You can group routes under a common prefix:

```php
Router::registerPrefix('/api/v1');

Router::add('/users', function() {
    echo "Users endpoint";
}, ['GET']);
```

All routes added after setting a prefix will automatically include it.

### Handling Errors

You can register custom error callbacks for `404` and `405` errors:

```php
Router::registerErrorCallback(OMNI_404, function($path) {
    echo "Error 404: The path $path was not found.";
});

Router::registerErrorCallback(OMNI_405, function($path, $method) {
    echo "Error 405: The method $method is not allowed for $path.";
});
```

### Registering Sub-Routers

For better modularization, you can load routes from external files:

```php
Router::registerSubRouter('path/to/routes.php');
```

### Running the Router

Finally, to execute the router and handle incoming requests, call the `run` method:

```php
Router::run();
```

## Example

Here’s a full example of setting up routes with OmniRoute:

```php
require_once 'path/to/Router.php';

use OmniRoute\Router;

// Register a prefix
Router::registerPrefix('/api/v1');

// Register routes
Router::add('/users', function() {
    echo "List of users";
}, ['GET']);

Router::add('/user/<:id:>', function($id) {
    echo "User ID: $id";
}, ['GET']);

// Register error callbacks
Router::registerErrorCallback(OMNI_404, function($path) {
    echo "404 Not Found: $path";
});

Router::registerErrorCallback(OMNI_405, function($path, $method) {
    echo "405 Method Not Allowed: $method for $path";
});

// Run the router
Router::run();
```

## License

This package is open-source and available under the MIT License.

---

For further details and documentation, refer to the source code and comments within the `Router.php` file. Contributions and issues are welcome.
