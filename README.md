# genuine-email-validator
A Laravel 5 Service to easily validate and verify if an Email Address is genuine or not. Uses the free MailboxLayer API (Quotas Apply).


# Requirements
* Laravel 5
* PHP 7
* Guzzle ^6.3


# Installation
`composer require JonathanPort/genuine-email-validator`


# Usage
```php

<?php

namespace App\Http\Controllers;

use JonathanPort\GenuineEmailValidator\GenuineEmailValidator;


class TestController extends Controller
{

  public function test(GenuineEmailValidator $validator)
  {

     $validator->emailAddressIsGenuine('hello@jonathanport.com'); // Returns Mailbox Feedback or false
     $validator->emailAddressIsValid('hello@jonathanport.com', $uniqueColumn = 'users'); // Returns simple Laravel Email Validator

  }

}


```


