# OmniRoute

**OmniRoute** is a lightweight, flexible PHP routing library designed to simplify the management of routes in your PHP applications. It allows you to define routes, handle URL parameters with regular expressions, and manage error callbacks for common HTTP response codes.

## Features

- **Route Registration**: Easily register routes with specific HTTP methods and callbacks.
- **Prefix Support**: Apply a prefix to a group of routes to organize and manage them efficiently.
- **Error Handling**: Register custom callbacks for handling `404 Not Found` and `405 Method Not Allowed` errors.
- **Regular Expression Arguments**: Support for dynamic URL parameters using regular expressions.
- **Sub-Router Integration**: Include and register routes from external files for better modularization.

## Installation

```
composer require jonahh/omniroute
```

## Usage

### Basic Route Registration

You can register a simple route by calling the `add` method:

```php
use OmniRoute\Router;
require __DIR__.'/vendor/autoload.php';

Router::add('/home', function() {
    echo "Welcome to the homepage!";
});
```

### Route with Parameters

To create a route with dynamic parameters, use placeholders wrapped in `<: :>`:

```php
Router::add('/user/<:id:>', function($id) {
    echo "User ID: " . $id;
});
```

### Prefixing Routes

You can group routes under a common prefix:

```php
Router::registerPrefix('/api/v1');

Router::add('/users', function() {
    echo "Users endpoint";
});
```

All routes added after setting a prefix will automatically include it.

### Registering Sub-Routers

For better modularization, you can load routes from external files:

```php
Router::registerSubRouter('path/to/routes.php');
```

### Setting allowed methods

You can set what methods are allowed for routes to be called:

```php

Router::add('/api/post-only', function() {
    echo json_encode(["data"=>$data]);
}, ["POST"]);
```

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

### Running the Router

Finally, to execute the router and handle incoming requests, call the `run` method:

```php
Router::run();
```

## License

This package is open-source and available under the MIT License.

## Author

This package is developed by [Jonah](https://github.com/Jonah987654321/)
