<?php

namespace JonathanPort\GenuineEmailValidator;

use Illuminate\Support\Facades\Validator;
use GuzzleHttp;


/*
|--------------------------------------------------------------------------
| GenuineEmailValidator::class
|--------------------------------------------------------------------------
|
| Email Checking Service containing two public methods:
|
| 1. $this->emailAddressIsGenuine($email)
| 2. $this->emailAddressIsValid($email, $unique = false)
|
| Note:
| MailboxLayer is a free API but has a limited request quota. There should be
| enough monthly quota to get you by for dev testing but to use for a
| production site, I would strongly suggest upgrading to the "Basic Plan".
| It's $9.99 / mo with a 20% discount if paid for yearly. $9.99 gets you 5000
| requests per month to play around with. Handy for new sites with limited
| traffic or small startup applicatons on a budget.
|
| Use sparingly and avoid making any crazy loops that could eat up
| your request quota and you'll be fine.
*/
class GenuineEmailValidator
{

    /**
     * MailboxLayer API Key
     *
     * @var string
     */
    private $mailboxKey;


    /**
     * MailboxLayer API Request Endpoint
     *
     * @var string
     */
    private $mailboxEndpoint;


    /**
     * Guzzle HTTP request Client
     *
     * @var object
     */
    private $guzzle;



    /**
     * Start new instance. Setup some common variables.
     *
     * @param string|null $key  Optionally pass an API key instead of using env()
     */
    public function __construct(string $key = null)
    {

        $this->key = $key ? $key : env('MAILBOXLAYER_KEY');

        $this->endpoint = 'https://apilayer.net/api/check';

        $this->guzzle = new GuzzleHttp\Client();

    }


    /**
     * Simple method to validate an email using standard Laravel
     * Email Address Validator method. Can optionally pass through
     * the name of a table you wish the email to be unique to.
     *
     * Note: This method is intended to be used the for the most common
     * case scenario. Of course build your own validation methods if you
     * need more advanced functionality.
     *
     * @param  string $email
     * @param  string $unique  The table name to apply unique rules to
     * @return bool
     */
    public function emailAddressIsValid(string $email, string $unique = null)
    {

        $validator = Validator::make(['email' => $email], [
            'email' => 'required|string|email|max:255' . ($unique ? "|unique:{$unique}" : '')
        ]);

        return $validator;

    }


    /**
     * Public Method to verify if an email is genuine.
     * This method uses the "MailboxLayer" API service.
     * This API can verify if an email addresses' MX records
     * are live or not.
     *
     * @param  string $email
     * @return mixed bool | object
     */
    public function emailAddressIsGenuine(string $email)
    {

        $res = $this->mailboxLayerRequest($email);

        return $res->mx_found ? $res : false;

    }


    /**
     * Makes the MialboxLayer request. If the status is not 200
     * or Mailbox returns any errors, a new Exception will be thrown.
     *
     * @param  string $email
     * @return object        JSON Decoded Mailbox Layer Response
     */
    private function mailboxLayerRequest(string $email)
    {

        // Make Request
        $res = $this->guzzle->request('GET', $this->endpoint($email));


        // Check status is 200 else throw new exception
        if ($res->getStatusCode() !== 200) {
            throw new \Exception('MailboxLayer API Request Failed.', $res->getStatusCode());
        }


        // Grab and decode request body
        $body = json_decode($res->getBody()->getContents());


        // Check if Mailbox has returned any errors
        if (isset($body->success) && ! $body->success) {
            throw new \Exception("{$body->error->type}: {$body->error->info}", $body->error->code);
        }


        // Return the Decoded Response Object
        return $body;

    }


    /**
     * Helper method to return the Mailbox Endpoint
     *
     * @param  string $email
     * @return string
     */
    private function endpoint(string $email)
    {
        return "{$this->endpoint}?access_key={$this->key}&email={$email}";
    }


}