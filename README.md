## Chronolabs Cooperative presents

# NTP Main Pooling Services

## Version: 1.0.4 (stable)

### Author: Dr. Simon Antony Roberts <simon@ordinance.space>

#### Demo: http://ntp.snails.email

This allows for an PHP Run API for people to mount NTP Services as free boat; on the main ntp.conf listing when they pinged existing...

# Apache Mod Rewrite (SEO Friendly URLS)

The follow lines go in your API_ROOT_PATH/.htaccess

    php_value memory_limit 24M
    php_value upload_max_filesize 1M
    php_value post_max_size 1M
    php_value error_reporting 0
    php_value display_errors 0
        
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    
    RewriteRule ^([a-z0-9]{2})/(editntp|addntp).(php|serial|json|xml|api)$ ./index.php?version=$1&mode=$2&format=$3 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/ntp.conf$ ./index.php?version=$1&mode=ntp.conf&format=conf [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/(top|new|worse).(rss)$ ./index.php?version=$1&mode=$2&format=$3 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/(online|offline|pings|uptime|downtime|nextping).(php|serial|json|xml)$ ./index.php?version=$1&mode=$2&format=$3 [L,NC,QSA]

## Scheduled Cron Job Details.,
    
There is one or more cron jobs that is scheduled task that need to be added to your system kernel when installing this API, the following command is before you install the chronological jobs with crontab in debain/ubuntu
    
    Execute:-
    $ sudo crontab -e


### CronTab Entry:

You have to add the following cronjobs to your cronjobs or on windows scheduled tasks!

    */1 * * * * /usr/bin/php /var/www/ntp.snails.email/crons/reports-ntp-services.php
    */1 * * * * /usr/bin/php /var/www/ntp.snails.email/crons/ping-ntp-services.php
    
    
## Licensing

 * This is released under General Public License 3 - GPL3 + ACADEMIC!

# Installation

Copy the contents of this archive/repository to the run time environment, configue apache2, ngix or iis to resolve the path of this repository and run the HTML Installer.
