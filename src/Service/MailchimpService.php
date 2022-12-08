<?php
require_once '../../vendor/autoload.php';

$mailchimp = new MailchimpMarketing\ApiClient();

$mailchimp->setConfig([
  'apiKey' => $this->getParameter('app.apikey'),
  'server' => $this->getParameter('app.serverprefix')
]);

$response = $mailchimp->ping->get();
print_r($response);
