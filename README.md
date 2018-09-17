# laravel-alpacajs
A package to build AlpacaJS forms via laravel


#### Laravel 5.4
Add the service provider to ```config/app.php```

```
    Pkeogan\LaravelAlpacaJS\LaravelAlpacaJSServiceProvider::class,
```
#### Laravel 5.5+
This package supports Laravel's Package Auto Discovery and should be automatically loaded when required using composer. If the package is not auto discovered run

```bash
    php artisan package:discover
```


