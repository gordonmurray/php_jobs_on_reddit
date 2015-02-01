<?php

require __DIR__ . '/bootstrap.php';

/*
 * Search reddit and email any new PHP related opportunities
 */
$app->get('/', function () use ($app, $opportunity) {

    $new_opportunities = $opportunity->retrieve_new_opportunities();

    $logged_opportunities = $opportunity->retrieve_logged_opportunities();

    $unseen_opportunities = $opportunity->determine_unseen_opportunities($new_opportunities, $logged_opportunities);

    $opportunity->send_email_notification($app, $unseen_opportunities);

    $opportunity->update_logged_opportunities($unseen_opportunities, $logged_opportunities);

    return count($unseen_opportunities) . ' opportunities found';

});

/*
 * Show a simple table of the recorded opportunities
 */
$app->get('/logs', function () use ($app, $opportunity) {

    $logged_opportunities = $opportunity->retrieve_logged_opportunities();

    return $app['twig']->render('logs.twig', array('logs' => $logged_opportunities));

});

$app->run();