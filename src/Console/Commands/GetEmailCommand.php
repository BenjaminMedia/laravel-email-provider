<?php

namespace Bonnier\EmailProvider\Commands;

use Bonnier\EmailProvider\EmailServiceProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Helper\ProgressBar;

class GetEmailCommand extends Command
{

    protected $signature = 'bonnier:email:get {--brand= : The ID of the brand} {--locale= : What locale to get}';

    protected $description = 'Get Emails from Email Manager';

    /** @var int */
    private $service_id;

    /** @var Client */
    private $client;

    /** @var string */
    private $templatePath;

    /** @var ProgressBar */
    private $saveBar;

    public function __construct()
    {
        parent::__construct();

        $this->service_id = config('services.email_manager.service_id');
        $this->client = new Client([
            'base_uri' => config('services.email_manager.url'),
        ]);
        $this->templatePath = EmailServiceProvider::getTemplatePath();
    }

    public function handle()
    {
        $brandId = $this->option('brand');
        $locale = $this->option('locale');

        $uri = '/api/v1/emails/service/'.$this->service_id;
        if($brandId && is_null($locale)) {
            $uri .= '/brand/'.$brandId;
        } elseif($brandId && $locale) {
            $uri .= '/brand/'.$brandId.'/locale/'.$locale;
        } elseif(is_null($brandId) && $locale) {
            $uri .= '/locale/'.$locale;
        }

        $this->info('Retrieving emails');
        try {
            $response = $this->client->get($uri);
        } catch(ClientException $e) {
            $this->error($e->getMessage());
            return;
        }

        $this->info('Parsing emails');
        $result = collect(json_decode($response->getBody()->getContents()));

        if(!$result->isEmpty()) {
            $this->info(sprintf('Fetched %s emails', $result->count()));
            $this->info('Saving Emails');
            $this->saveBar = $this->output->createProgressBar($result->count());
            $this->saveEmails($result);
            $this->saveBar->finish();
        } else {
            $this->error('Nothing found');
        }
        $this->info(PHP_EOL. 'Done');
    }

    /**
     * @param Collection $emails
     */
    private function saveEmails(Collection $emails)
    {
        $emails->each(function($email){
            if(!$this->createEmailTemplate($email)) {
                $this->error('Failed to write email template for: ' . json_encode($email));
            }
            $this->saveBar->advance();
        });
    }

    private function createEmailTemplate($email)
    {
        $structure = $email->locale.DIRECTORY_SEPARATOR.$email->brand_id;
        $path = $this->templatePath.DIRECTORY_SEPARATOR.$structure;

        if(!File::exists($path))
        {
            if(!File::makeDirectory($path, 0770, true))
            {
                $this->error(sprintf('Could not make directory \'%s\'', $structure));
            }
        }

        $filePath = $path.DIRECTORY_SEPARATOR.$email->key.'.json';

        return File::put($filePath, json_encode($email));
    }
}
