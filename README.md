## Chronolabs Cooperative presents

# Network Time Protocol - REST API Services

## Version: 1.0.3 (stable)

### Author: Dr. Simon Antony Roberts <wishcraft@users.sourceforge.net>

#### Demo: http://ntp.snails.email

This API is written for internal use of chronolabs cooperative, you can find it running on http://172.104.177.252

# Apache Mod Rewrite (SEO Friendly URLS)

The follow lines go in your API_ROOT_PATH/.htaccess

    php_value memory_limit 25M
    php_value upload_max_filesize 10M
    php_value post_max_size 10M
    
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    
    RewriteRule ^([a-z0-9]{2})/(editntp|addntp).(asp|php|serial|json|xml|api)$ ./index.php?version=$1&mode=$2&format=$3 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/ntp.conf$ ./index.php?version=$1&mode=ntp.conf&format=conf [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/(top|new|worse).(rss)$ ./index.php?version=$1&mode=$2&format=$3 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/(online|offline|pings|uptime|downtime|nextping).(asp|php|serial|json|xml)$ ./index.php?version=$1&mode=$2&format=$3 [L,NC,QSA]
    RewriteRule ^index.html$ ./index.php [L]

## Scheduled Cron Job Details.,
    
There is one or more cron jobs that is scheduled task that need to be added to your system kernel when installing this API, the following command is before you install the chronological jobs with crontab in debain/ubuntu
    
    Execute:-
    $ sudo crontab -e


### CronTab Entry:

You have to add the following cronjobs to your cronjobs or on windows scheduled tasks!

    * */22 * * * /usr/bin/php /var/www/html/crons/0.ntp-more-services.php
    * */22 * * * /usr/bin/php /var/www/html/crons/1.ntp-more-services.php
    * */22 * * * /usr/bin/php /var/www/html/crons/ntp-more-services.php
    * */22 * * * /usr/bin/php /var/www/html/crons/ntp1-more-services.php
    * */22 * * * /usr/bin/php /var/www/html/crons/clock-more-services.php
    * */22 * * * /usr/bin/php /var/www/html/crons/time-more-services.php
    */1 * * * * /usr/bin/php /var/www/html/crons/ntp-mining-services.php
    */1 * * * * /usr/bin/php /var/www/html/crons/ping-ntp-services.php
    */1 * * * * /usr/bin/php  /var/www/html/crons/reports-ntp-services.php
    */11 * * * *  mysql ntp-snails-email < /var/www/html/crons/querys.sql && unlink /var/www/html/crons/querys.sql
    
    
## Licensing

 * This is released under General Public License 3 - GPL3 + ACADEMIC!

# Installation

Copy the contents of this archive/repository to the run time environment, configue apache2, ngix or iis to resolve the path of this repository and created the database then set the settings in the following files:

    /mainfile.php
    /include/dbconfig.php
    /include/constants.php
