<?php


namespace Bonnier\EmailProvider\Helpers;

use Bonnier\ContextService\Context\Context;
use Bonnier\EmailProvider\EmailServiceProvider;
use Bonnier\EmailProvider\Models\EmailTemplate;
use Illuminate\Support\Facades\File;

class BonnierMail
{
    /**
     * @param       $key
     * @param array $replace
     * @param null  $locale
     *
     * @return \Bonnier\EmailProvider\Models\EmailTemplate
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function get($key, $replace = [], $locale = null) : EmailTemplate
    {
    	$brand = 'default';
    	if(app(Context::class)->getBrand()) {
    		$brand = app(Context::class)->getBrand()->getId();
    	}

    	$email = json_decode(File::get(
            EmailServiceProvider::getTemplatePath().DIRECTORY_SEPARATOR.
            $locale.DIRECTORY_SEPARATOR.
            $brand.DIRECTORY_SEPARATOR.
            $key.'.json'
        ));

        return new EmailTemplate($email, $replace);
    }
}
