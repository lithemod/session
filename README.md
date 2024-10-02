# Session

The session middleware in Lithe is responsible for managing user sessions, allowing you to store and retrieve persistent information between requests, such as login data and preferences.

## 1. Installing the Session Middleware

To use the session middleware in Lithe, you need to install it via Composer. Composer is a dependency management tool for PHP.

### Step by Step:

1. **Open the terminal (command line)**.
2. **Navigate to your project directory**. Use the `cd` command to change to the directory where your Lithe project is located. For example:
   ```bash
   cd /path/to/your/project
   ```

3. **Run the installation command**:
   ```bash
   composer require lithemod/session
   ```

This command will download and install the session middleware and its dependencies.

## 2. Configuring the Session Middleware

After installing the middleware, you need to configure it in your Lithe application. This is done using the `use()` method.

### Example Configuration:

```php
use function Lithe\Middleware\Session\session;

// Add the middleware to the application
$app->use(session());
```

### Configuration Parameters

You can configure the session middleware with some important parameters:

- **lifetime**: Sets the session duration in seconds. The default is 2592000 (30 days).
- **domain**: Sets the domain for which the session cookie is valid.
- **secure**: Indicates whether the session cookie should only be sent over secure connections (HTTPS).
- **httponly**: If it should only be accessible via HTTP requests.
- **samesite**: Defines the SameSite attribute of the session cookie. It can be `'Lax'`, `'Strict'`, or `'None'`.
- **path**: Defines the path where the session files will be stored.

### Example Configuration with Parameters:

```php
$app->use(session([
    'lifetime' => 3600, // 1 hour
    'domain' => 'example.com',
    'secure' => true, // Only over HTTPS
    'httponly' => true, // Accessible only via HTTP
    'samesite' => 'Strict', // SameSite policy
    'path' => 'storage/framework/session', // Path to store sessions
]));
```

## 3. Using Session Variables

After configuration, you can access and manipulate session variables through the `Request` object. Let’s see how to do this through routes.

### Route Examples

Here are some examples of how to use session variables in Lithe routes.

#### Setting a Session Variable

```php
$app->get('/set-user', function ($req, $res) {
    $req->session->put('user', 'John Doe'); // Set the session variable
    return $res->send('User set in the session!');
});
```

#### Retrieving a Session Variable

```php
$app->get('/get-user', function ($req, $res) {
    $user = $req->session->get('user', 'User not found'); // Retrieve the session variable
    return $res->send('User: ' . $user);
});
```

#### Removing a Session Variable

```php
$app->get('/remove-user', function ($req, $res) {
    $req->session->forget('user'); // Remove the session variable
    return $res->send('User removed from the session!');
});
```

#### Destroying All Session Variables

```php
$app->get('/destroy-session', function ($req, $res) {
    $req->session->destroy(); // Destroy all session variables
    return $res->send('All session variables have been destroyed!');
});
```

#### Checking if the Session is Active

```php
$app->get('/check-session', function ($req, $res) {
    $isActive = $req->session->isActive(); // Check if the session is active
    return $res->send('Session active: ' . ($isActive ? 'Yes' : 'No'));
});
```

#### Regenerating the Session ID

```php
$app->get('/regenerate-session', function ($req, $res) {
    $req->session->regenerate(); // Regenerate the session ID
    return $res->send('Session ID regenerated!');
});
```

#### Getting the Session ID

```php
$app->get('/session-id', function ($req, $res) {
    $sessionId = $req->session->getId(); // Get the session ID
    return $res->send('Session ID: ' . $sessionId);
});
```

#### Setting a New Session ID

```php
$app->get('/set-session-id', function ($req, $res) {
    $req->session->setId('newSessionId'); // Set a new ID for the session
    return $res->send('New session ID set!');
});
```

#### Getting All Session Variables

```php
$app->get('/all-session-data', function ($req, $res) {
    $allSessionData = $req->session->all(); // Get all session variables
    return $res->send('Session data: ' . json_encode($allSessionData));
});
```

#### Checking if a Session Variable Exists

```php
$app->get('/has-user', function ($req, $res) {
    $hasUser = $req->session->has('user'); // Check if the session variable 'user' exists
    return $res->send('User in session: ' . ($hasUser ? 'Yes' : 'No'));
});
```

## 4. Magic Methods

The session object also provides some magic methods for convenience:

- **`__get($key)`**: Retrieves the value of a session variable.
  
  ```php
  $user = $req->session->user; // Equivalent to $req->session->get('user');
  ```

- **`__set($key, $value)`**: Sets the value of a session variable.
  
  ```php
  $req->session->user = 'Jane Doe'; // Equivalent to $req->session->put('user', 'Jane Doe');
  ```

## Final Considerations

- **Creating the Session Directory**: The middleware ensures that the directory for storing sessions exists. If it does not, it will be created automatically.
- **Error Handling**: If any errors occur during session configuration or initialization, the middleware will log them and continue execution normally.
