<?php

namespace Bonnier\EmailProvider;

use Bonnier\ContextService\Context\Context;
use Bonnier\ContextService\Models\BpBrand;
use Bonnier\EmailProvider\Commands\GetEmailCommand;
use Illuminate\Support\ServiceProvider;

class EmailServiceProvider extends ServiceProvider
{

    protected $commands = [
        GetEmailCommand::class
    ];

    private static $emailPath;

    private static $brandId;

    public function boot()
    {
        /** @var BpBrand $brand */
        $brand = app(Context::class)->getBrand();
        if($brand) {
            self::$brandId = $brand->getId();
        } else {
            self::$brandId = 'default';
        }
    }

    public function register()
    {
        $this->commands($this->commands);
    }

    public static function getTemplatePath()
    {
        if(!self::$emailPath) {
            self::$emailPath = storage_path('vendor/email-manager/emails');
        }
        return self::$emailPath;
    }

    public static function getBrandId()
    {
        return self::$brandId;
    }
}
