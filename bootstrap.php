<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application();

$opportunity = new \Opportunity\opportunity();

$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
));

$app->register(new Silex\Provider\SwiftmailerServiceProvider());

use Symfony\Component\Yaml\Parser;

$yaml = new Parser();

$email_settings = $yaml->parse(file_get_contents('./email_settings.yml'));

$app['swiftmailer.options'] = array(
    'host' => $email_settings['host'],
    'port' => $email_settings['port'],
    'username' => $email_settings['username'],
    'password' => $email_settings['password'],
    'encryption' => 'ssl',
    'auth_mode' => null,
);