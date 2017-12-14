<?php

namespace Bonnier\EmailProvider\Models;

class EmailTemplate
{
    private $emailManagerTemplate;
    private $tokenReplacements;

    /**
     * EmailTemplate constructor.
     *
     * @param \stdClass $emailManagerTemplate
     * @param array     $tokenReplacements
     */
    public function __construct(\stdClass $emailManagerTemplate, array $tokenReplacements)
    {
        $this->emailManagerTemplate = $emailManagerTemplate;
        $this->tokenReplacements = $tokenReplacements;
    }

    /**
     * @return mixed
     */
    public function getEmailManagerTemplate()
    {
        return $this->emailManagerTemplate;
    }

    /**
     * @param mixed $emailManagerTemplate
     */
    public function setEmailManagerTemplate($emailManagerTemplate)
    {
        $this->emailManagerTemplate = $emailManagerTemplate;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->replaceTokens($this->emailManagerTemplate->subject);
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return nl2br(
            $this->replaceTokens($this->emailManagerTemplate->body)
        );
    }

    private function getFormattedTokens()
    {
        return collect(array_keys($this->tokenReplacements))->map(function($token){
            return sprintf(':%s:', $token);
        })->toArray();
    }

    private function replaceTokens($tokenString)
    {
        return str_replace($this->getFormattedTokens(), array_values($this->tokenReplacements), $tokenString);
    }
}
