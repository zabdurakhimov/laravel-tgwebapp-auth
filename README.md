# Telegram WebApp User Authentication Module

[Telegram WebApp Documentation](https://core.telegram.org/bots/webapps)

Use Case: When developing an API for a Telegram WebApp, it is necessary to verify that the user who sent the request to the API is indeed the one they claim to be (i.e., the request actually came from the Telegram WebApp).

## How It Works

1. The Telegram WebApp JS script retrieves the WebAppUser object from the API and sends it in every request to the API in the request header (the header name is configurable).
2. The Guard receives the request and extracts the WebAppUser object from it.
3. The Guard verifies the data signature using the BOT_TOKEN.
4. The Guard looks for the user in the database:
   1. If the user is found, they are authenticated. 
   2. If the user is not found:
      1. If automatic user creation is allowed, the user will be created and authenticated. 
      2. If automatic user creation is disabled, a 403 error is returned.

## Configuration

In the `config/auth.php` file, the `tgwebapp` guard must be registered.

Example of the file content after registering the guard:

```text
...
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'tgwebapp' => [
        'driver' => 'tgwebapp', // the name of the guard
        'token' => env('TELEGRAM_BOT_TOKEN'), // bot token
        'autoCreation' => true, // flag allowing automatic user creation
        'userDataHeaderName' => 'X-TELEGRAM-USER-DATA', // header name from which the guard retrieves the WebAppUser object
        'userModel' => \App\Models\User::class, // user model class
    ]
],
...
```

## Usage

Include the guard in the routing file `routes/web.php`.

```text
...
Route::middleware('auth:tgwebapp')->group(function(){
    Route::get('/me', function(){
        return 'Hello!';
    });
});
...
```

A GET request to /me will go through authentication via the Telegram WebApp guard.