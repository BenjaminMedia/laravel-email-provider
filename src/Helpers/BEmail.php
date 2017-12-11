<?php


namespace Bonnier\EmailProvider\Helpers;

use Bonnier\ContextService\Context\Context;
use Bonnier\EmailProvider\EmailServiceProvider;
use Illuminate\Support\Facades\File;
use Parsedown;

class BonnierMail
{
    /**
     * @param       $key
     * @param array $replace
     * @param null  $locale
     *
     * @return array ie ['subject' => 'some subject', 'body' => 'some body']
     */
    public static function get($key, $replace = [], $locale = null) : array
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

    	$tokens = static::format_tokens(array_keys($replace));

        return [
            'subject' => $email->subject,
            'body' => Parsedown::instance()->parse(
                str_replace($tokens, array_values($replace), $email->body)
            )
        ];
    }

    private static function format_tokens($tokens)
    {
        return collect($tokens)->map(function($token){
            return sprintf(':%s:', $token);
        })->toArray();
    }
}
