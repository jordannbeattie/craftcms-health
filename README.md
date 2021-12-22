# CraftCMS Health
See simple checks for the health of your CraftCMS site and get notifications if they fail!

## Installation
```
composer require jordanbeattie/craftcms-health
./craft plugin/install health
./craft plugin/enable health
```

## Checks
The current checks are currently setup. If you have a request for another check, get in touch. 

### Environment
Checks that the `ENVIRONMENT` variable has been set and is equal to either _dev_, _staging_ or _production_.

### SMTP
Checks that SMTP is being used to send email.

### Mailhog
Checks that Mailhog is in use in _dev_ and not in any other environment. 

### HTTPS
Checks that HTTPS is being used in the Site URL. Will be marked as not-applicable on _dev_.

### Sitemap
Checks that your-site-url/sitemap.xml is readable. 
If ether/seo is also installed, it will check that at least 1 section is enabled to be listed in the sitemap. 

### Robots
Checks that our-site-url/robots.txt is readable and ensures that robots are not blocked on _production_ but are blocked in other environments.

### SEO Plugin
Checks that ether/seo is installed.

## Results
You can view the checks in the utilities section at our-site-url/admin/utilities/health or by running the command
``` 
./craft health/check 
```

Results have 3 parts: Title of the check, status (pass/fail) and text which will display any relevant comments. Often explaining why the check has failed.

## Notifications
All notifications are sent via Slack. You can add your [Slack webhook](https://slack.com/intl/en-gb/help/articles/115005265063-Incoming-webhooks-for-Slack) and channel in the settings section.
To receive notifications of failed checks, add the `--notify` option to the above command. 
```
./craft health/check --notify
```
This will send a Slack notification for failed checks only. 

### Why don't you send notifications via email?
Part of the checks is to ensure that email delivery is successful. If there was an issue sending emails from the site, notifications would also not be sent.
In a future update, we look to add more notification options. 
