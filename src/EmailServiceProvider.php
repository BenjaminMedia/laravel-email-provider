<?php

namespace Bonnier\EmailProvider;

use Bonnier\ContextService\Context\Context;
use Bonnier\ContextService\Models\BpBrand;
use Bonnier\EmailProvider\Commands\GetEmailCommand;
use Illuminate\Support\ServiceProvider;

class EmailServiceProvider extends ServiceProvider
{
    const TRANSLATION_NAMESPACE = 'bonnier';

    protected $commands = [
        GetEmailCommand::class
    ];

    private static $translationPath;

    private static $brandId;

    public function boot()
    {
        $this->loadTranslationsFrom(self::getTemplatePath(), self::TRANSLATION_NAMESPACE);

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
        if(!self::$translationPath) {
            self::$translationPath = storage_path('vendor/email-manager/emails');
        }
        return self::$translationPath;
    }

    public static function getBrandId()
    {
        return self::$brandId;
    }
}
