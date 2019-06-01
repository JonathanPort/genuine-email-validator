A simple Laravel 5 Service to easily validate and verify if an Email Address is genuine or not. Uses the free MailboxLayer API (Quotas Apply).


# Requirements
* Laravel 5
* PHP 7
* Guzzle ^6.3
* Mailbox Layer Account


# Installation
`composer require JonathanPort/genuine-email-validator`


# Usage
Before using, make sure to add your Mailbox Layer API key into your project's .env under: `MAILBOXLAYER_KEY`. Alternatively, you can pass the key through to the class when making a new instance:

```php
$service = new GenuineEmailValidator($key = 'YOUR_KEY_HERE');
```

To use the service, create a new instance of the service or pass the service as a controller method parameter via dependency injection. 

The service contains two public methods:


```php

<?php

namespace App\Http\Controllers;

use JonathanPort\GenuineEmailValidator\GenuineEmailValidator;


class TestController extends Controller
{

  public function test(GenuineEmailValidator $service)
  {
    
     // Returns Mailbox Feedback or false
     $service->emailAddressIsGenuine('hello@jonathanport.com');
     
     // Returns standard Laravel Email Validator Instance
     $service->emailAddressIsValid('hello@jonathanport.com', $uniqueColumn = 'users');

  }

}


```

# MailboxLayer API Usage Notes
Note:
MailboxLayer is a free API but has a limited request quota. There should be enough monthly quota to get you by for dev testing but to use for a production site, I would strongly suggest upgrading to the "Basic Plan". It's $9.99 / mo with a 20% discount if paid for yearly. $9.99 gets you 5000 requests per month to play around with. Handy for new sites with limited traffic or small startup applicatons on a budget.

Use sparingly and avoid making any crazy loops that could eat up your request quota and you'll be fine.


