# PHP jobs on Reddit.

This is a small application to look for new PHP related opportunities on Reddit's /forhire subreddit and email them to me.

The application logs opportunities to a file in /logs so that it doesn't send the same email notification multiple times.

## Installation

1. git clone git@github.com:murrion/php_jobs_on_reddit.git

2. php composer install

3. Update the email.settings.yml file with your own email host, port, username and password so the application can send emails to you.

4. Schedule the application to run often

5. (Optional) You can change some keywords to watch out for in the $search_keywords variable in the /src/opportunity.php file

#### Example Cron usage:
```
0 * * * * wget http://localhost/php_jobs_on_reddit
```

#### View logs:

http://localhost/php_jobs_on_reddit/index.php/logs

