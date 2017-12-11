# Laravel Translation Provider
Laravel package that retrieves email translations from translation manager 


## Usage
To fetch the email

`BEmail::get($key, $replace, $locale)`

## Getting translations
To get all translations from Email Manager, run:

`php artisan bonnier:translation:get`

## Setup
- `composer require bonnier/laravel-email-provider`
- Register the provider in config/app.php`
```php
    ...
    'providers' => [
        ...
       Bonnier\EmailProvider\EmailServiceProvider::class, 
    ],
```
- Setup configuration in `.env`
```
EMAIL_MANAGER_SERVICE_ID=1
EMAIL_MANAGER_URL=http://url-to-translationmanager.com
```
- Set up in `config/services.php`
```php
    'email_manager' => [
        'url' => env('EMAIL_MANAGER_URL'),
        'service_id' => env('EMAIL_MANAGER_SERVICE_ID'),
    ],
```
- Set up in `config/app.php`
```php
    'aliases' => [
        ...,
        'BonnierMail' => \Bonnier\EmailProvider\Helpers\BonnierMail::class,
    ]
```
