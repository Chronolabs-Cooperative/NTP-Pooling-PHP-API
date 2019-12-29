<?php
define('API_URL', 'http://ntp.snails.email:80');
define('API_ZONE_DOMAIN', 'snails.email');
define('API_ZONE_SUBDOMAIN', '%s.%s.ntp.snails.email');

$parts = explode('.', microtime(true));
mt_srand((int)($parts[1] * (int)($parts[0]/ 1000)));


require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'mainfile.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'functions.php';


if ($memory = APICache::read('memory-state-online~'.md5(API_ZONE_DOMAIN)))
    if (isset($memory['limit']) && !empty($memory['limit']))
        ini_set('memory_limit', $memory['limit']);
        
if (!$results = APICache::read('state-online~'.md5(API_ZONE_DOMAIN))) {
    if (!$caching = APICache::read('caching-online~'.md5(API_ZONE_DOMAIN))) 
        APICache::write('caching-online~'.md5(API_ZONE_DOMAIN), array('start'=>microtime(true)), (isset($memory['seconds'])?$memory['seconds']:(33 * 66 * 1.125)) * 1.335);
    $caching = APICache::read('caching-online~'.md5(API_ZONE_DOMAIN));
    if (time() > $caching['start'] + (isset($memory['seconds'])?$memory['seconds']:(33 * 66)) * 1.335) {
        echo "\n\nHost Online\n\n";
        echo "Array Elements: " . count(getHostsKeys('online', 'json'));
        $memory = APICache::read('memory-state-online~'.md5(API_ZONE_DOMAIN));
        APICache::write('caching-online~'.md5(API_ZONE_DOMAIN), array('start'=>microtime(true)), (isset($memory['seconds'])?$memory['seconds']:(33 * 66 * 1.125)) * 1.335);
    }
} else {
    echo "\n\nHost Online\n\n";
    echo "Array Elements: " . count($results);
}
if ($memory = APICache::read('memory-state-offline~'.md5(API_ZONE_DOMAIN)))
    if (isset($memory['limit']) && !empty($memory['limit']))
        ini_set('memory_limit', $memory['limit']);
        
if (!$results = APICache::read('state-offline~'.md5(API_ZONE_DOMAIN))) {
    if (!$caching = APICache::read('caching-offline~'.md5(API_ZONE_DOMAIN))) 
        APICache::write('caching-offline~'.md5(API_ZONE_DOMAIN), array('start'=>microtime(true)), (isset($memory['seconds'])?$memory['seconds']:(33 * 66 * 1.125)) * 1.335);
    $caching = APICache::read('caching-offline~'.md5(API_ZONE_DOMAIN));
    if (time() > $caching['start'] + (isset($memory['seconds'])?$memory['seconds']:(33 * 66)) * 1.335) {
        echo "\n\nHost Online\n\n";
        echo "Array Elements: " . count(getHostsKeys('offline', 'json'));
        $memory = APICache::read('memory-state-offline~'.md5(API_ZONE_DOMAIN));
        APICache::write('caching-offline~'.md5(API_ZONE_DOMAIN), array('start'=>microtime(true)), (isset($memory['seconds'])?$memory['seconds']:(33 * 66 * 1.125)) * 1.335);
    }
} else {
    echo "\n\nHost Online\n\n";
    echo "Array Elements: " . count($results);
}

if (!$ntpconf = APICache::read('ntp.conf~'.md5(API_ZONE_DOMAIN))) {
    echo "\n\n/etc/ntp.conf\n\n";
    echo "Array Elements: " . count(getNTPConf('ntp.conf', 'conf'));
} else {
    echo "\n\n/etc/ntp.conf\n\n";
    echo "Array Lines: " . count($ntpconf);
}