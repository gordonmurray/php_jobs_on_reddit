<?php

namespace Opportunity;


class Opportunity
{

    private $sender_email_address = 'noreply@domain.com';

    private $recipient_email_address = 'me@domain.com';

    private $log_file = './logs/logged_opportunities.txt';

    private $search_keywords = 'php,api,codeigniter,laravel,silex,developer,webdeveloper';


    /**
     * Compare old and new opportunities and return only the unseen ones
     *
     * @param $new_opportunities
     * @param $logged_opportunities
     * @return array
     */
    public function determine_unseen_opportunities($new_opportunities, $logged_opportunities)
    {
        $unseen_opportunities = array();

        if (is_array($new_opportunities) && is_array($logged_opportunities) && !empty($new_opportunities)) {
            $unseen_opportunities = array_diff_assoc($new_opportunities, $logged_opportunities);
        }

        return $unseen_opportunities;
    }

    /**
     * update the list of opportunities already seen
     *
     * @param $unseen_opportunities
     * @param $logged_opportunities
     */
    public function update_logged_opportunities($unseen_opportunities, $logged_opportunities)
    {
        $posts_array = array_merge($unseen_opportunities, $logged_opportunities);
        $fp = fopen($this->log_file, 'w');
        fwrite($fp, serialize($posts_array));
        fclose($fp);
    }

    /**
     * email me about new opportunities
     *
     * @param $app
     * @param $new_opportunities
     */
    public function send_email_notification($app, $new_opportunities)
    {
        if (!empty($new_opportunities)) {

            foreach ($new_opportunities as $opportunity) {

                $message = \Swift_Message::newInstance()
                    ->setSubject('PHP Opportunity: ' . $opportunity['title'])
                    ->setContentType('text/html')
                    ->setFrom(array($this->sender_email_address))
                    ->setTo(array($this->recipient_email_address))
                    ->setBody($app['twig']->render('email_notification.twig', array(
                        'title' => $opportunity['title'],
                        'content' => $opportunity['content'],
                        'author' => $opportunity['author'],
                        'comments' => $opportunity['comments'],
                        'created' => $opportunity['created'],
                        'url' => $opportunity['url']
                    )));

                $app['mailer']->send($message);
            }
        }
    }

    /**
     * Retrieve an array of forhire posts from reddit
     *
     * @return mixed
     */
    public function retrieve_new_opportunities()
    {
        $search_keywords = explode(",", $this->search_keywords);

        $reddit_forhire_json = file_get_contents('http://www.reddit.com/r/forhire/new/.json');

        $reddit_forhire_array = json_decode($reddit_forhire_json, true);

        $threads = $reddit_forhire_array['data']['children'];

        foreach ($threads as $thread) {
            $title = strtolower($thread['data']['title']);

            if (stristr($title, 'hiring') == true) {

                $title_words = explode(" ", $title);

                if (count(array_intersect($title_words, $search_keywords)) > 0) {
                    $opportunities[$thread['data']['id']] = array(
                        'title' => $thread['data']['title'],
                        'content' => html_entity_decode($thread['data']['selftext_html']),
                        'author' => $thread['data']['author'],
                        'comments' => $thread['data']['num_comments'],
                        'created' => date("d/m/Y g:ia", $thread['data']['created_utc']),
                        'url' => $thread['data']['url']
                    );
                }
            }
        }

        return $opportunities;
    }

    /**
     * Return an array of any previously recorded job opportunities
     * Used to avoid emailing the same thing twice.
     *
     * @return array
     */
    public function retrieve_logged_opportunities()
    {
        $opportunities_past = array();

        if (file_exists($this->log_file)) {
            $handle = fopen($this->log_file, 'r');
            if (filesize($this->log_file) > 0) {
                $opportunities_past = unserialize(fread($handle, filesize($this->log_file)));
            }
        }

        return $opportunities_past;
    }
}