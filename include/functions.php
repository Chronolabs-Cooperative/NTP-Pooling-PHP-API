<?php
/**
 * Email Account Propogation REST Services API
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Chronolabs Cooperative http://syd.au.snails.email
 * @license         ACADEMIC APL 2 (https://sourceforge.net/u/chronolabscoop/wiki/Academic%20Public%20License%2C%20version%202.0/)
 * @license         GNU GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @package         emails-api
 * @since           1.1.11
 * @author          Dr. Simon Antony Roberts <simon@snails.email>
 * @version         1.1.11
 * @description		A REST API for the creation and management of emails/forwarders and domain name parks for email
 * @link            http://internetfounder.wordpress.com
 * @link            https://github.com/Chronolabs-Cooperative/Emails-API-PHP
 * @link            https://sourceforge.net/p/chronolabs-cooperative
 * @link            https://facebook.com/ChronolabsCoop
 * @link            https://twitter.com/ChronolabsCoop
 * 
 */


if (!function_exists("formatRssTimestamp")) {
    function formatRssTimestamp($time)
    {
        $date = gmdate('D, d M Y H:i:s', (int)$time) . $TIME_ZONE;
        return $date;
    }
}

if (!function_exists("addNTP")) {

    function addNTP($hostname, $port, $name, $nameemail, $nameurl, $companyname, $companyemail, $companyrbn, $companyrbntype, $companytype, $companyurl, $format) 
    {
        require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'apimailer.php';
        if (validateEmail($nameemail) || (!validateEmail($nameemail) && !validateEmail($companyemail))) {
            if (validateEmail($companyemail) || (!validateEmail($nameemail) && !validateEmail($companyemail))) {
                list($count) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF("SELECT count(*) as `count` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE `hostname` = '" . $GLOBALS['APIDB']->escape($hostname) . " AND `port` = '" . $GLOBALS['APIDB']->escape($port) . "'"));
                if ($count == 0) {
                    $GLOBALS['APIDB']->queryF("START TRANSACTION");
                    $sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` (`typal`, `state`, `hostname`, `port`, `name`, `nameemail`, `nameurl`, `companyname`, `companyemail`, `companyrbn`, `companyrbntype`, `companytype`, `companyurl`) VALUES('pool', 'bucky', '" . $GLOBALS['APIDB']->escape($hostname) . "', '" . $GLOBALS['APIDB']->escape($port) . "', '" . $GLOBALS['APIDB']->escape($name) . "', '" . $GLOBALS['APIDB']->escape($nameemail) . "', '" . $GLOBALS['APIDB']->escape($nameurl) . "', '" . $GLOBALS['APIDB']->escape($companyname) . "', '" . $GLOBALS['APIDB']->escape($companyemail) . "', '" . $GLOBALS['APIDB']->escape($companyrbn) . "', '" . $GLOBALS['APIDB']->escape($companyrbntype) . "', '" . $GLOBALS['APIDB']->escape($companytype) . "', '" . $GLOBALS['APIDB']->escape($companyurl) . "')";
                    if ($GLOBALS['APIDB']->queryF($sql)) {
                        $GLOBALS['APIDB']->queryF("COMMIT");
                        $ntpid = $GLOBALS['APIDB']->getInsertId();
                        $sql = "SELECT * FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE `id` = $ntpid";
                        $result = $GLOBALS['APIDB']->queryF($sql);
                        $ntpservice = $GLOBALS['APIDB']->fetchArray($result);
                        $tmpplate = array();
                        unset($ntpservice['mored']);
                        $ntpservice['key'] = md5($ntpservice['id'].$ntpservice['nameemail'].$ntpservice['companyemail'].'ntpservice'.API_URL);
                        $ntpservice['form'] = getHTMLForm('editntp', $ntpservice);
                        $ntpservice['api_url'] = API_URL;
                        $ntpservice['pinging'] = number_format($ntpservice['pinging'], 2) . 'ms';
                        $tmpplate['admin'] = $ntpservice['state'];
                        foreach(array('pinged', 'prevping', 'emailed', 'reportnext', 'reportlast', 'online', 'offline', 'updated') as $field)
                            $ntpservice[$field] = date("Y-m-d, W, D, H:i:s", $ntpservice[$field]);
                        foreach(array('uptime', 'downtime') as $field) {
                            if ($ntpservice[$field]==1)
                                $ntpservice[$field] = 0;
                                $ntpservice[$field] = formatMSASTime($ntpservice[$field] * 1000);
                        }
                        $mailer = new APIMailer("chronolabscoop@users.sourceforge.net", "Chronolabs Coop (ntp.snails.email)");
                        $body = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'email__service_added.html');
                        $subject = sprintf("Added %s - to %s - Network Transit Protocol (NTP)", $ntpservice['hostname'], API_URL);
                        foreach($ntpservice as $field => $value)
                            $body = str_replace("%$field", $value, $body);
                        if ($mailer->sendMail(array($ntpservice['companyemail'] => $ntpservice['companyname']), array($ntpservice['nameemail'] => $ntpservice['name']), array('chronolabcoop@outlook.com' => 'Chronolabs Coop (BCC)'), $subject, $body, array(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'Organisational Timing-bell.pdf', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'Organisational Timing-bell.docx', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'Organisational Timing-bell.odt'), array(), true )) 
                            @$GLOBALS['APIDB']->queryF($sql = "UPDATE `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` SET `uptime` = 0, `downtime` = 0, `emailed` = UNIX_TIMESTAMP(), `updated` = UNIX_TIMESTAMP() WHERE `id` = '" . $ntpid . "'");
                        return array('code' => 201, 'errors' => array(), 'key' => md5($ntpid.$nameemail.$companyemail.'ntpservice'.API_URL));
                    } else {
                        if (file_exists(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'crons' . DIRECTORY_SEPARATOR . 'querys.sql'))
                            $querys = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'querys.sql');
                        else
                            $querys = "## Queries to process on mysql: " . date("Y-m-d D, W, H:i:s") . "\n##\n## Cron job:-\n##\n## */1 * * * * mysql < \"" . __DIR__ . DIRECTORY_SEPARATOR . "querys.sql\" && unlink \"" . __DIR__ . DIRECTORY_SEPARATOR . "querys.sql\"\n##\n##\n\nuse `" . API_DB_NAME . "`;\n\n";
                        $querys .= $sql . ";\n";
                        file_put_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'crons' . DIRECTORY_SEPARATOR . 'querys.sql', $querys);
                        return array('code' => 501, 'errors' => array($GLOBALS['APIDB']->errno => "Error with SQL: $sql;"));
                    }
                } else {
                    return array('code' => 501, 'errors' => array("Hostname already exists: $hostname:$port;"));
                }
            } else {
                return array('code' => 501, 'errors' => array(101 => 'Company Email doesn\'t conform to email standard!'));
            }
        } else {
            return array('code' => 501, 'errors' => array(102 => 'Telephanist Email doesn\'t conform to email standard!'));
        }
        return false;
    }
}

if (!function_exists("editNTP")) {
    
    /**
     *
     */
    function editNTP($key, $hostname, $port, $name, $nameemail, $nameurl, $companyname, $companyemail, $companyrbn, $companyrbntype, $companytype, $companyurl, $format) 
    {
        $sql = "SELECT `id` WHERE 'state' = 'bucky' AND '$key' = md5(concat(`id`,'ntpservice','".API_URL."'))";
        list($id) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF($sql));
        if ($id <> 0) {
            if (validateEmail($nameemail) || (!validateEmail($nameemail) && !validateEmail($companyemail))) {
                if (validateEmail($companyemail) || (!validateEmail($nameemail) && !validateEmail($companyemail))) {
                    $sql = "UPDATE `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` SET `state` = 'assigned', `hostname` = '" . $GLOBALS['APIDB']->escape($hostname) . "', `port` = '" . $GLOBALS['APIDB']->escape($port) . "', `name` = '" . $GLOBALS['APIDB']->escape($name) . "', `nameemail`  = '" . $GLOBALS['APIDB']->escape($nameemail) . "', `nameurl` = '" . $GLOBALS['APIDB']->escape($nameurl) . "', `companyname` = '" . $GLOBALS['APIDB']->escape($companyname) . "', `companyemail` = '" . $GLOBALS['APIDB']->escape($companyemail) . "', `companyrbn` = '" . $GLOBALS['APIDB']->escape($companyrbn) . "', `companyrbntype` = '" . $GLOBALS['APIDB']->escape($compnayrbntype) . "', `companytype` = '" . $GLOBALS['APIDB']->escape($companytype) . "', `companyurl` = '" . $GLOBALS['APIDB']->escape($companyurl) . "' WHERE `id` = '$id'";
                    if ($GLOBALS['APIDB']->queryF($sql)) {
                        return array('code' => 201, 'errors' => array(), 'key' => md5($id.$nameemail.$companyemail.'ntpservice'.API_URL));
                    } else {
                        return array('code' => 501, 'errors' => array($GLOBALS['APIDB']->errno =>  "Error with SQL: $sql;"));
                    }
                } else {
                    return array('code' => 501, 'errors' => array(101 => 'Company Email doesn\'t conform to email standard!'));
                }
            } else {
                return array('code' => 501, 'errors' => array(102 => 'Telephanist Email doesn\'t conform to email standard!'));
            }
        } else {
            return array('code' => 501, 'errors' => array(101 => 'Key doesn\'t match any records!'));
        }
        return false;
    }
}


function checkEmail($email, $antispam = false)
{
    if (!$email || !preg_match('/^[^@]{1,64}@[^@]{1,255}$/', $email)) {
        return false;
    }
    $email_array      = explode('@', $email);
    $local_array      = explode('.', $email_array[0]);
    $local_arrayCount = count($local_array);
    for ($i = 0; $i < $local_arrayCount; ++$i) {
        if (!preg_match("/^(([A-Za-z0-9!#$%&'*+\/\=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/\=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i])) {
            return false;
        }
    }
    if (!preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1])) {
        $domain_array = explode('.', $email_array[1]);
        if (count($domain_array) < 2) {
            return false; // Not enough parts to domain
        }
        for ($i = 0; $i < count($domain_array); ++$i) {
            if (!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", $domain_array[$i])) {
                return false;
            }
        }
    }
    if ($antispam) {
        $email = str_replace('@', ' at ', $email);
        $email = str_replace('.', ' dot ', $email);
    }
    
    return $email;
}


if (!function_exists("getHostsKeys")) {
    
    function getHostsKeys($mode, $format, $pooler = 0, $poolers = 0)
    {
        if ($memory = APICache::read('memory-state-'.$mode.'-'.$pooler.'.'.$poolers))
            if (isset($memory['limit']) && !empty($memory['limit']))
                ini_set('memory_limit', $memory['limit']);
                
        if (!$results = APICache::read('state-'.$mode.'-'.$pooler.'.'.$poolers)) {
            switch ($mode) {
                case 'online':
                    if ($pooler <= $poolers && $pooler > 0) {
                        $countsql = "SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE `pinging` > 0 AND `uptime` > `downtime` AND `uptime` > 0 ORDER BY `pinging` DESC";
                        list($count) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF($countsql));
                        $limit = floor($count / $poolers);
                        $sql = "SELECT *, md5(concat(`id`,'ntpservice','".API_URL."')) as `key` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE `pinging` > 0 AND `uptime` > `downtime` AND `uptime` > 0 ORDER BY `pinging` DESC LIMIT " . ($limit * ($pooler - 1) + 1) . ', ' . $limit;
                    } else 
                        $sql = "SELECT *, md5(concat(`id`,'ntpservice','".API_URL."')) as `key` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE `pinging` > 0 AND `uptime` > `downtime` AND `uptime` > 0 ORDER BY `pinging` DESC";
                    break;
                case 'offline':
                    $sql = "SELECT *, md5(concat(`id`,'ntpservice','".API_URL."')) as `key` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE `pinging` = 0 AND `uptime` <= `downtime` ORDER BY `downtime` DESC";
                    break;
            }
            $mb = 71;
            $datas = $properties = $memory = $results = array();
            $result = $GLOBALS['APIDB']->queryF($sql);
            while($row = $GLOBALS['APIDB']->fetchArray($result))
            {
                $key = nef($row['hostname']);
                unset($row['state']);
                unset($row['id']);
                $properties[$key] = json_decode($row['properties'], true);
                $datas[$key] = json_decode($row['data'], true);
                unset($row['properties']);
                unset($row['data']);
                $row['nameemail'] = checkEmail($row['nameemail'], true);
                $row['companyemail'] = checkEmail($row['companyemail'], true);
                foreach(array('pinged', 'prevping', 'emailed', 'reportnext', 'reportlast', 'online', 'offline', 'updated') as $field)
                    if (!empty($row[$field]))
                        $row[$field] = date('Y-m-d, D, H:i:s', $row[$field]);
                    else
                        unset($row[$field]);
                foreach(array('uptime', 'downtime') as $field)
                    if (!empty($row[$field]))
                        $row[$field] = formatMSASTime($row[$field]);
                    else
                        unset($row[$field]);
                $row['timezone'] = date_default_timezone_get();
                $mb = $mb + ((strlen(json_encode($row)) / (1024 * 1024 * 1024)) * 2);
                ini_set('memory_limit', $memory['limit'] = floor($mb) . 'M');
                if (!isset($results[$key]))
                    $results[$key] = $row;
                                            
            }
            APICache::write('state-'.$mode.'-pooling.'.$pooler.'of'.$poolers, $results, $sec = mt_rand(15, 60) * mt_rand(20, 445));
            
            $authkey = json_decode(getURIData(str_replace('%apiurl', API_ZONE_URL, API_ZONE_AUTHKEY) . "?" . http_build_query(array('username' => API_ZONE_USERNAME, 'password' => API_ZONE_PASSWORD, 'format' => 'json')), 7, 11, array()), true);
            $domains = json_decode(getURIData(str_replace('%apiurl', API_ZONE_URL, str_replace('%authkey', $authkey['authkey'], API_ZONE_DOMAINKEYS)), 7, 11, array()), true);
            
            if (isset($domains['domains']) && is_array($domains['domains']))
                foreach($domains['domains'] as $domain) {
                    if ($domain['name'] == API_ZONE_DOMAIN || $domain['master'] == API_ZONE_DOMAIN) {
                        if (!defined("API_ZONE_DOMAINKEY"))
                            define("API_ZONE_DOMAINKEY", $domain['domainkey']);
                    }
                }
            
            if (defined("API_ZONE_DOMAINKEY") && $_REQUEST['mode'] == 'online') {
                
                $records = json_decode(getURIData(str_replace('%apiurl', API_ZONE_URL, str_replace('%authkey', $authkey['authkey'], str_replace('%domainkey', API_ZONE_DOMAINKEY, API_ZONE_DNSRECORDIRECTORY_SEPARATOR))), 7, 11, array()), true);
                foreach($results as $key => $pool) {
                    $hostname = $pool['hostname'];
                    if (!in_array(API_ZONE_DOMAIN, $properties[$key]['sites'][$hostname])) {
                        $properties[$key]['sites'][$hostname][API_ZONE_DOMAIN] = API_ZONE_DOMAIN;
                        if (!$pooling = APICache::read(API_ZONE_DOMAINKEY.$mode.'-pooling'))
                            $pooling = array();
                        if (!in_array($hostname, array_keys($pooling))) {
                            $pooling[$hostname]['hostname'] = $hostname;
                            $pooling[$hostname]['alias'] = sprintf(API_ZONE_SUBDOMAIN, hash('adler32', $hostname), $pool['typal']);
                            $pooling[$hostname]['alias-wildcard'] = '*.'.sprintf(API_ZONE_SUBDOMAIN, hash('adler32', $hostname), $pool['typal']);
                            $datas[$key]['typal'][API_ZONE_DOMAIN][$hostname] = $pooling[$hostname]['typal'] = $pool['typal'];
                            
                            if (!in_array($hostname, array_keys($datas[$key]['alias'][API_ZONE_DOMAIN]))) {
                                $datas[$key]['alias'][API_ZONE_DOMAIN][$hostname] = $pooling[$hostname]['alias'];
                                
                                if (!APICache::read($pooling[$hostname]['alias'].'-'.$pooling[$hostname]['host'])) {
                                    $apiaction = false;
                                    if (isset($records['zones']) && is_array($records['zones'])) 
                                        foreach($records['zones'] as $record) {
                                            if ($record['name'] == $pooling[$hostname]['alias'] && $record['type'] == API_ZONE_CNAMETYPE) {
                                                if ($record['content'] != $hostname) {
                                                    @getURIData((str_replace('%apiurl', API_ZONE_URL, str_replace('%authkey', $authkey['authkey'], str_replace('%recordkey', $record['recordkey'], API_ZONE_EDITRECORD))) . '?' . http_build_query(array('content'=>$hostname))), 7, 11, array());
                                                }
                                            }
                                            
                                            $apiaction = true;
                                            APICache::write($pooling[$hostname]['alias'].'-'.$pooling[$hostname]['hostname'], array('time'=>time()), time());
                                        }
                                    if (isset($hostname) && !empty($hostname) && $apiaction!=true) {
                                        @getURIData((str_replace('%apiurl', API_ZONE_URL, str_replace('%authkey', $authkey['authkey'], API_ZONE_ADDRECORD)) .'?'. http_build_query(array('domain' => API_ZONE_DOMAINKEY, 'type' => API_ZONE_CNAMETYPE, 'name' => sprintf(API_ZONE_SUBDOMAIN, hash('adler32', $hostname), $row['typal']), 'content'=>$hostname, 'ttl' => 6000, 'prio' => 5, 'format' => 'json'))), 7, 11, array());
                                        APICache::write($pooling[$hostname]['alias'].'-'.$pooling[$hostname]['hostname'], array('time'=>time()), time());
                                        $apiaction = true;
                                    }
                                    APICache::write($pooling[$hostname]['alias'].'-'.$pooling[$hostname]['hostname'], array('time'=>time()), time());
                                }
                            }
                            if (!in_array($hostname, array_keys($datas[$key]['alias-wildcard'][API_ZONE_DOMAIN]))) {
                                $datas[$key]['alias-wildcard'][API_ZONE_DOMAIN][$hostname] = $pooling[$hostname]['alias-wildcard'];
                                if (!APICache::read('uno.'.$pooling[$hostname]['alias'].'-'.$pooling[$hostname]['alias'])) {
                                    $apiactionb = false;
                                    if (isset($records['zones']) && is_array($records['zones']))
                                        foreach($records['zones'] as $record) {
                                            if ($record['name'] == $pooling[$hostname]['alias-wildcard'] && $record['type'] == API_ZONE_CNAMETYPE) {
                                                if ($record['content'] != sprintf(API_ZONE_SUBDOMAIN, hash('adler32', $hostname), $row['typal'])) {
                                                    @getURIData((str_replace('%apiurl', API_ZONE_URL, str_replace('%authkey', $authkey['authkey'], str_replace('%recordkey', $record['recordkey'], API_ZONE_EDITRECORD))) . '?' . http_build_query(array('content'=>sprintf(API_ZONE_SUBDOMAIN, hash('adler32', $hostname), $row['typal'])))), 7, 11, array());
                                                }
                                                $apiactionb = true;
                                                APICache::write('uno.'.$pooling[$hostname]['alias'].'-'.$pooling[$hostname]['alias'], array('time'=>time()), time());
                                            }
                                        }
                                    if ($apiactionb!=true) {
                                        @getURIData((str_replace('%apiurl', API_ZONE_URL, str_replace('%authkey', $authkey['authkey'], API_ZONE_ADDRECORD)) .'?'. http_build_query(array('domain' => API_ZONE_DOMAINKEY, 'type' => API_ZONE_CNAMETYPE, 'name' => sprintf(API_ZONE_SUBDOMAIN, '*.' . hash('adler32', $hostname), $row['typal']), 'content'=>sprintf(API_ZONE_SUBDOMAIN, hash('adler32', $hostname), $row['typal']), 'ttl' => 6000, 'prio' => 5, 'format' => 'json'))), 7, 11, array());
                                        $apiactionb = true;
                                        APICache::write('uno.'.$pooling[$hostname]['alias'].'-'.$pooling[$hostname]['alias'], array('time'=>time()), time());
                                    }
                                    APICache::write('uno.'.$pooling[$hostname]['alias'].'-'.$pooling[$hostname]['alias'], array('time'=>time()), time());
                                }
                            }
                            APICache::write(API_ZONE_DOMAINKEY.$mode.'-pooling', $pooling, time());
                        } 
                        $GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` SET `properties` = '" . $GLOBALS['APIDB']->escape(json_encode($properties[$key])) . "' AND `data` = '" . $GLOBALS['APIDB']->escape(json_encode($datas[$key])) . "' WHERE `hostname` LIKE '" . $results[$key]['hostname'] . "' AND `port` = '" . $results[$key]['port'] . "'");
                        
                    }
                    $results[$key]['alias'] = $datas[$key]['alias'][API_ZONE_DOMAIN][$hostname];
                    $results[$key]['alias-wildcard'] = $datas[$key]['alias-wildcard'][API_ZONE_DOMAIN][$hostname];
                    $results[$key]['typal'] = $datas[$key]['typal'][API_ZONE_DOMAIN][$hostname];
                }
            }
            APICache::write('state-'.$mode.'-pooling.'.$pooler.'of'.$poolers, $results, $sec = mt_rand(15, 60) * mt_rand(20, 445));
            APICache::write('memory-state-'.$mode.'-pooling.'.$pooler.'of'.$poolers, $memory, $sec * 2);
        }
        return $results;
    }
}

if (!function_exists("getCompaniesKeys")) {
    
    function getCompaniesKeys($mode, $format) 
    {
        if (!$results = APICache::read('companies-'.$mode.'~'.md5(API_ZONE_DOMAIN))) {
            $start = microtime(true);
            switch ($mode) {
                case 'companies':
                    $sql = "SELECT DISTINCT `companyname`, `companyemail`, `companyurl`, `companyrbn`, `companyrbntype`, md5(concat(`companyname`,'companies','".API_URL."')) as `key` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE LENGTH(`companyname`) > 0 AND `uptime` > `downtime` AND `uptime` > 0";
                    break;
                case 'offcompanies':
                    $sql = "SELECT DISTINCT `companyname`, `companyemail`, `companyurl`, `companyrbn`, `companyrbntype`, md5(concat(`companyname`,'companies','".API_URL."')) as `key` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE LENGTH(`companyname`) > 0 AND `uptime` < `downtime`";
                    break;
            }
            $mb = 82;
            $memory = $results = array();
            $result = $GLOBALS['APIDB']->queryF($sql);
            while($row = $GLOBALS['APIDB']->fetchArray($result))
            {
                $row['companyemail'] = checkEmail($row['companyemail'], true);
                $results[nef($row['companyname'])] = $row;
                APICache::write('companies-'.$mode.'~'.md5(API_ZONE_DOMAIN), $results, $sec = microtime(true) - $start * mt_rand(1353, 2899));
            }
            APICache::write('companies-'.$mode.'~'.md5(API_ZONE_DOMAIN), $results, $sec = microtime(true) - $start * mt_rand(1353, 2899));
            
        }                    
        return $results;
    }
}


if (!function_exists("getHostsLestatsKeys")) {
    
    function getHostsLestatsKeys($mode, $format) 
    {
        if ($memory = APICache::read('memory-stats-'.$mode.'~'.md5(API_ZONE_DOMAIN)))
            if (isset($memory['limit']) && !empty($memory['limit']))
                ini_set('memory_limit', $memory['limit']);
                
            if (!$results = APICache::read('state-'.$mode.'~'.md5(API_ZONE_DOMAIN))) {
            switch ($mode) {
                case 'pings':
                    $sql = "SELECT md5(concat(`id`,'ntpservice','".API_URL."')) as `key`, `hostname`, `pinging` as `pinging-in-ms` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE `pinging` > 0 ORDER BY `pinging` ASC";
                    break;
                case 'uptime':
                    $sql = "SELECT md5(concat(`id`,'ntpservice','".API_URL."')) as `key`, `hostname`, `uptime` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE `uptime` > `downtime` AND `uptime` > 0  ORDER BY `uptime` DESC";
                    break;
                case 'downtime':
                    $sql = "SELECT md5(concat(`id`,'ntpservice','".API_URL."')) as `key`, `hostname`, `downtime` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE `uptime` <= `downtime` ORDER BY `downtime` DESC";
                    break;
                case 'nextping':
                    $sql = "SELECT md5(concat(`id`,'ntpservice','".API_URL."')) as `key`, `hostname`, `pinged` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE `pinged` > UNIX_TIMESTAMP() ORDER BY `pinged` ASC";
                    break;
            }
            $mb = 82;
            $memory = $results = array();
            $result = $GLOBALS['APIDB']->queryF($sql);
            while($row = $GLOBALS['APIDB']->fetchArray($result))
            {
                $key = nef($row['hostname']);
                if (isset($row['uptime']))
                    $row['uptime'] = formatMSASTime($row['uptime'] * 1000);
                if (isset($row['downtime']))
                    $row['downtime'] = formatMSASTime($row['downtime'] * 1000);
                if (isset($row['pinged']))
                    $row['pinged'] = date("Y-m-d W, D, H:i:s", $row['pinged']);
                $mb = $mb + ((strlen(json_encode($row)) / (1024 * 1024 * 1024)) * 2);
                ini_set('memory_limit', $memory['limit'] = floor($mb) . 'M');
                if (!isset($results[$key]))
                    $results[$key] = $row;
            }
            APICache::write('stats-'.$mode.'~'.md5(API_ZONE_DOMAIN), $result, $sec = mt_rand(41, 199) * mt_rand(253, 711));
            APICache::write('memory-stats-'.$mode.'~'.md5(API_ZONE_DOMAIN), $memory, $sec * 2);
        }
        return $results;
    }
}


if (!function_exists('nef'))
{
    
    function nef($subject = '', $stripe ='-')
    {
        $replacements = array("one" => "1", "two" => "2", "three" => "3", "four" => "4", "five" => "5", "six" => "6", "seven" => "7", "eight" => "8", "nine" => "9", "zero" => "0");
        foreach($replacements as $replace => $search)
            $subject = str_replace($search, $replace, $subject);
        return sef($subject, $stripe);
    }
}

if (!function_exists('sef'))
{
    
    function sef($value = '', $stripe ='-')
    {
        return yonkOnlyAlphanumeric($value, $stripe);
    }
}


if (!function_exists('yonkOnlyAlphanumeric'))
{
    
    function yonkOnlyAlphanumeric($value = '', $stripe ='-')
    {
        $replacement_chars = array();
        $accepted = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","m","o","p","q",
            "r","s","t","u","v","w","x","y","z","0","9","8","7","6","5","4","3","2","1");
        for($i=0;$i<256;$i++){
            if (!in_array(strtolower(chr($i)),$accepted))
                $replacement_chars[] = chr($i);
        }
        $result = trim(str_replace($replacement_chars, $stripe, strtolower($value)));
        while(strpos($result, $stripe.$stripe, 0))
            $result = (str_replace($stripe.$stripe, $stripe, $result));
        while(substr($result, 0, strlen($stripe)) == $stripe)
            $result = substr($result, strlen($stripe), strlen($result) - strlen($stripe));
        while(substr($result, strlen($result) - strlen($stripe), strlen($stripe)) == $stripe)
            $result = substr($result, 0, strlen($result) - strlen($stripe));
        return($result);
    }
}

if (!function_exists("getHostsRSS")) {
    
    function getHostsRSS($mode, $items, $format) {
                
        if (true!=false||!$feed = APICache::read('rss-'.$mode)) {
            $feed = array();
            $feed['link'] = API_URL;
            $feed['lastbuild'] = formatRssTimestamp(time());
            $feed['image_url'] = API_URL . '/assets/images/logo_500x500.png';
            $feed['image_width'] = 500;
            $feed['image_height'] = 500;
            switch ($mode) {
                case 'top':
                    $feed['title'] = "Top NTP Services on: " . API_URL;
                    $feed['desc'] = "This is the top NTP Services on: " . API_URL . " ~ they can variable and variate from time to time!";
                    $sql = "SELECT md5(concat(`id`,'ntpservice','".API_URL."')) as `key`, `pinging`, `pinged`, `uptime`, `downtime`, sha1(concat((`uptime` / `pinging`), `id`,'ntpservice','".API_URL."','".$mode."')) as `guid`, `hostname`, `port`, `name`, `nameurl`, `companyname`, `companyurl`, `online` as `pubDate` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE (`uptime` / `pinging`) > 0 HAVING (`pinged` < UNIX_TIMESTAMP() AND `pinging` > 1.99997 AND `uptime` > 0) ORDER BY (`uptime` / `pinging`) DESC LIMIT " . ($items>0?$items:25);
                    break;
                case 'worse':
                    $feed['title'] = "Worse NTP Services on: " . API_URL;
                    $feed['desc'] = "This is the worst NTP Services on: " . API_URL . " ~ they can variable and variate from time to time!";
                    $sql = "SELECT md5(concat(`id`,'ntpservice','".API_URL."')) as `key`, `pinging`, `pinged`, `uptime`, `downtime`, sha1(concat((`downtime` * `pinging`), `id`,'ntpservice','".API_URL."','".$mode."')) as `guid`, `hostname`, `port`, `name`, `nameurl`, `companyname`, `companyurl`, `offline` as `pubDate` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE (`downtime` * `pinging`) > 0 HAVING (`pinged` < UNIX_TIMESTAMP() AND `pinging` > 1.99997  AND `downtime` > 0) ORDER BY (`downtime` * `pinging`) DESC LIMIT " . ($items>0?$items:25);
                    break;
                case 'new':
                    $feed['title'] = "New NTP Services on: " . API_URL;
                    $feed['desc'] = "This is the new NTP Services on: " . API_URL . " ~ they can variable and variate from time to time!";
                    $sql = "SELECT md5(concat(`id`,'ntpservice','".API_URL."')) as `key`, `pinging`, `pinged`, `uptime`, `downtime`, sha1(concat(`created`, `id`,'ntpservice','".API_URL."','".$mode."')) as `guid`, `hostname`, `port`, `name`, `nameurl`, `companyname`, `companyurl`, UNIX_TIMESTAMP() as `pubDate` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` ORDER BY `created` DESC LIMIT " . ($items>0?$items:25);
                    break;
            }
 
            $results = array();
            $result = $GLOBALS['APIDB']->queryF($sql);
            while($row = $GLOBALS['APIDB']->fetchArray($result))
            {
                $key = nef($row['hostname']);
                $row['title'] = $row['companyname'] . ' ~ [ ' . $row['hostname'] . ":" . $row['port'] . ' ] :: [ Ping: ' . number_format($row['pinging'], 4) . 'ms' . ' ]';
                if (isset($row['uptime']))
                    $row['uptime'] = formatMSASTime($row['uptime']);
                if (isset($row['downtime']))
                    $row['downtime'] = formatMSASTime($row['downtime']);
                if (isset($row['pinging']))
                    $row['pinging'] = number_format($row['pinging'], 4) . 'ms';
                if (isset($row['pinged']))
                    $row['pinged'] = date("Y-m-d W, D, H:i:s", $row['pinged']);
                if (isset($row['pubDate']))
                    $row['pubDate'] = formatRssTimestamp($row['pubDate']);
                if (!isset($results[$key]))
                    $results[$key] = $row;
            }
            $items = array();
            if (count($results)) {
                $feeditem = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'item.xml');
                foreach($results as $key => $result) {
                    $items[$key] = $feeditem;
                    $keys = array_keys($result);
                    sort($keys, SORT_DESC);
                    foreach($keys as $field)
                        $items[$key] = str_replace("%$field", $result[$field], $items[$key]);
                }
                $feed['items'] = implode("\n\n", $items);
            }
            APICache::write('rss-'.$mode, $feed, $sec = mt_rand(15, 60) * mt_rand(20, 445));
        }
        $feedxml = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'feed.xml');
        foreach($feed as $field => $value)
            $feedxml = str_replace("%$field", $value, $feedxml);
        ob_end_clean();
        header('Content-type: text/rss+xml');
        die(trim($feedxml));
    }
}

if (!function_exists("getURIData")) {
    
    /* function yonkURIData()
     *
     * 	Get a supporting domain system for the API
     * @author 		Simon Roberts (Chronolabs) simon@labs.coop
     *
     * @return 		float()
     */
    function getURIData($uri = '', $timeout = 25, $connectout = 25, $post = array(), $headers = array())
    {
        if (!function_exists("curl_init"))
        {
            die("Install PHP Curl Extension ie: $ sudo apt-get install php-curl -y");
        }
        $GLOBALS['php-curl'][md5($uri)] = array();
        if (!$btt = curl_init($uri)) {
            return false;
        }
        if (count($post)==0 || empty($post))
            curl_setopt($btt, CURLOPT_POST, false);
        else {
            $uploadfile = false;
            foreach($post as $field => $value)
                if (substr($value , 0, 1) == '@' && !file_exists(substr($value , 1, strlen($value) - 1)))
                    unset($post[$field]);
                else
                    $uploadfile = true;
            curl_setopt($btt, CURLOPT_POST, true);
            curl_setopt($btt, CURLOPT_POSTFIELDIRECTORY_SEPARATOR, http_build_query($post));
            
            if (!empty($headers))
                foreach($headers as $key => $value)
                    if ($uploadfile==true && substr($value, 0, strlen('Content-Type:')) == 'Content-Type:')
                        unset($headers[$key]);
            if ($uploadfile==true)
                $headers[]  = 'Content-Type: multipart/form-data';
        }
        if (count($headers)==0 || empty($headers)) {
            curl_setopt($btt, CURLOPT_HEADER, false);
            curl_setopt($btt, CURLOPT_HTTPHEADER, array());
        } else {
            curl_setopt($btt, CURLOPT_HEADER, false);
            curl_setopt($btt, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($btt, CURLOPT_CONNECTTIMEOUT, $connectout);
        curl_setopt($btt, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($btt, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($btt, CURLOPT_VERBOSE, false);
        curl_setopt($btt, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($btt, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($btt);
        $GLOBALS['php-curl'][md5($uri)]['http']['uri'] = $uri;
        $GLOBALS['php-curl'][md5($uri)]['http']['posts'] = $post;
        $GLOBALS['php-curl'][md5($uri)]['http']['headers'] = $headers;
        $GLOBALS['php-curl'][md5($uri)]['http']['code'] = curl_getinfo($btt, CURLINFO_HTTP_CODE);
        $GLOBALS['php-curl'][md5($uri)]['header']['size'] = curl_getinfo($btt, CURLINFO_HEADER_SIZE);
        $GLOBALS['php-curl'][md5($uri)]['header']['value'] = curl_getinfo($btt, CURLINFO_HEADER_OUT);
        $GLOBALS['php-curl'][md5($uri)]['size']['download'] = curl_getinfo($btt, CURLINFO_SIZE_DOWNLOAD);
        $GLOBALS['php-curl'][md5($uri)]['size']['upload'] = curl_getinfo($btt, CURLINFO_SIZE_UPLOAD);
        $GLOBALS['php-curl'][md5($uri)]['content']['length']['download'] = curl_getinfo($btt, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        $GLOBALS['php-curl'][md5($uri)]['content']['length']['upload'] = curl_getinfo($btt, CURLINFO_CONTENT_LENGTH_UPLOAD);
        $GLOBALS['php-curl'][md5($uri)]['content']['type'] = curl_getinfo($btt, CURLINFO_CONTENT_TYPE);
        curl_close($btt);
        return $data;
    }
}

/**
 * validateMD5()
 * Validates an MD5 Checksum
 *
 * @param string $email
 * @return boolean
 */

if (!function_exists("validateMD5")) {
    function validateMD5($md5) {
        if(preg_match("/^[a-f0-9]{32}$/i", $md5)) {
            return true;
        } else {
            return false;
        }
    }
}

/**
 * validateEmail()
 * Validates an Email Address
 *
 * @param string $email
 * @return boolean
 */
if (!function_exists("validateEmail")) {
    function validateEmail($email) {
        if(preg_match("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.([0-9]{1,3})|([a-zA-Z]{2,3})|(aero|coop|info|mobi|asia|museum|name|edu))$", $email)) {
            return true;
        } else {
            return false;
        }
    }
}

/**
 * validateDomain()
 * Validates a Domain Name
 *
 * @param string $domain
 * @return boolean
 */
if (!function_exists("validateDomain")) {
    function validateDomain($domain) {
        if(!preg_match("/^([-a-z0-9]{2,100})\.([a-z\.]{2,8})$/i", $domain)) {
            return false;
        }
        return $domain;
    }
}

/**
 * validateIPv4()
 * Validates and IPv6 Address
 *
 * @param string $ip
 * @return boolean
 */
if (!function_exists("validateIPv4")) {
    function validateIPv4($ip) {
        if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) === FALSE) // returns IP is valid
        {
            return false;
        } else {
            return true;
        }
    }
}
/**
 * validateIPv6()
 * Validates and IPv6 Address
 *
 * @param string $ip
 * @return boolean
 */
if (!function_exists("validateIPv6")) {
    function validateIPv6($ip) {
        if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === FALSE) // returns IP is valid
        {
            return false;
        } else {
            return true;
        }
    }
}

if (!function_exists("getHostnamePing")) {
    
    /* function getHostnamePing()
     *
     * @author 		Simon Roberts (Chronolabs) simon@ordinance.space
     *
     * @return 		float()
     */
    function getHostnamePing($hostname = '::1')
    {
        $result = array();
        ob_start();
        if (validateIPv6($hostname))
            exec('ping6  -c ' . mt_rand(5,13) . " $hostname", $result);
        else
            exec('ping -c ' . mt_rand(5,13) . " $hostname", $result);
        $output = implode("\n", $result) . ob_get_clean();
        ob_end_clean();
        if (!strpos($output, "100% packet loss") || !strpos($output, "not known") || !strpos($output, "name resolution")) {
            $parts = explode('\n', $output);
            $parts = explode("/", $parts[count($parts) - 1]);
            return (float)$parts[4];
        }
        if (strpos($output, "100% packet loss"))
        {
            if (getTimeFromNTP($hostname, 123) > 0) {
                $step = mt_rand(5,13);
                $odds = array();
                for($r=1;$r<=$step;$r++) {
                    $start = microtime(true) * 1000;
                    $val = getTimeFromNTP($hostname, 123);
                    $odds[$start][microtime(true) * 1000] = $val;
                }
                $avg = array();
                foreach($odds as $start => $odd)
                    foreach($odd as $finish => $value)
                        $avg[] = $finish - $start;
                $result = 0;
                foreach($avg as $id => $value)
                    if ($id>0)
                        $result = $result + $value /2;
                    else {
                        $result = $value;
                    }
                return $result;
            }
        }
        elseif (strpos($output, "not known") || !strpos($output, "name resolution"))
            return -1;
    }
}

/**
 * Returns UNIX timestamp from a NTP server (RFC 5905)
 *
 * @param  string  $host    Server host (default is pool.ntp.org)
 * @param  integer $timeout Timeout  in seconds (default is 10 seconds)
 * @return integer Number of seconds since January 1st 1970
 */
function getTimeFromNTP($host = 'ntp.snails.email', $port = 123, $timeout = 42)
{
    $socket = stream_socket_client('udp://' . $host . ':' . $port, $errno, $errstr, (int)$timeout);
    $msg = "\010" . str_repeat("\0", 47);
    fwrite($socket, $msg);
    $response = fread($socket, 48);
    fclose($socket);
    // unpack to unsigned long
    $data = unpack('N12', $response);
    // 9 =  Receive Timestamp (rec): Time at the server when the request arrived
    // from the client, in NTP timestamp format.
    $timestamp = sprintf('%u', $data[9]);
    // NTP = number of seconds since January 1st, 1900
    // Unix time = seconds since January 1st, 1970
    // remove 70 years in seconds to get unix timestamp from NTP time
    $timestamp -= 2208988800;
    return (integer)$timestamp;
}

if (!function_exists("getHostnameNTPPing")) {
    
    /* function getHostnamePing()
     *
     * @author 		Simon Roberts (Chronolabs) simon@ordinance.space
     *
     * @return 		float()
     */
    function getHostnameNTPPing($hostname = '::1', $port = 123)
    {
        $output = array();
        $ms = $num = 0;
        for($l=0; $l < mt_rand(7,19); $l++) {
            $start = microtime(true);
            $time = getTimeFromNTP($hostname, $port);
            if ($time<>0) {
                $ms += (microtime(true)) - ($start);
                $num++;
            }
        }
        if ($num>0)
            return $ms / $num;
        return false;
    }
}


if (!function_exists("formatMSASTime")) {
    
    /* function formatMSASTime()
     *
     * @author 		Simon Roberts (Chronolabs) simon@ordinance.space
     *
     * @return 		float()
     */
    function formatMSASTime($milliseconds = '')
    {
        $return = '';
        $milliseconds = $milliseconds;
        if (($milliseconds / (3600 * 24 * 7 * 4 * 12)) >= 1)
        {
            $scratch = (string)($milliseconds / (3600 * 24 * 7 * 4 * 12));
            $parts = explode(".", $scratch);
            $return .= $parts[0] . ' year' .($parts[0]>1?"s ":" ");
            $milliseconds = ((float)("0." . $parts[1])) * (3600 * 24 * 7 * 4 * 12);
        }
        if (($milliseconds / (3600 * 24 * 7 * 4)) >= 1)
        {
            $scratch = (string)($milliseconds / (3600 * 24 * 7 * 4));
            $parts = explode(".", $scratch);
            $return .= $parts[0] . ' month' .($parts[0]>1?"s ":" ");
            $milliseconds = ((float)("0." . $parts[1])) * (3600 * 24 * 7 * 4);
        }
        if (($milliseconds / (3600 * 24 * 7)) >= 1)
        {
            $scratch = (string)($milliseconds /(3600 * 24 * 7));
            $parts = explode(".", $scratch);
            $return .= $parts[0] . ' week' .($parts[0]>1?"s ":" ");
            $milliseconds = ((float)("0." . $parts[1])) * (3600 * 24 * 7);
        }
        if (($milliseconds / (3600*24)) >= 1)
        {
            $scratch = (string)($milliseconds / (3600 * 24));
            $parts = explode(".", $scratch);
            $return .= $parts[0] . ' day' .($parts[0]>1?"s ":" ");
            $milliseconds = ((float)("0." . $parts[1])) * (3600 * 24);
        }
        if (($milliseconds / 3600) >= 1)
        {
            $scratch = (string)($milliseconds / 3600);
            $parts = explode(".", $scratch);
            $return .= $parts[0] . ' hour' .($parts[0]>1?"s ":" ");
            $milliseconds = ((float)("0." . $parts[1])) * 3600;
        }
        if (($milliseconds / 60) >= 1)
        {
            $scratch = (string)($milliseconds / 60);
            $parts = explode(".", $scratch);
            $return .= $parts[0] . ' min' .($parts[0]>1?"s ":" ");
            $milliseconds = ((float)("0." . $parts[1])) * 60;
        }
        if (($milliseconds / 60) >= 1)
        {
            $scratch = (string)($milliseconds / 60);
            $parts = explode(".", $scratch);
            $return .= $parts[0] . ' sec' .($parts[0]>1?"s ":" ");
            $milliseconds = ((float)("0." . $parts[1])) * 60;
        }
        if (empty($return))
            $return = 'No Time Passed!';
        
        return $return = trim($return);
    }
}

if (!function_exists("convert2ASP")) {
    function convert2ASP($data = array()) {
        $datatext = '';
        foreach($data as $key => $values)
            if (is_array($values)) {
                foreach($values as $name => $value)
                    $datatext . 'resultsNTP("' . $key . '")("' . $name . '") = "' . $value . "\"\n\r";
            } else
                $datatext . 'resultsNTP("' . $key . '") = "' . $values . "\"\n\r";
        return str_replace('%DATA%', $datatext, file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'asptemplate.txt'));
    }
}
    
if (!function_exists("getNTPConf")) {
    
    /* function getNTPConf()
     *
     * 	Get a supporting domain system for the API
     * @author 		Simon Roberts (Chronolabs) simon@labs.coop
     *
     * @return 		string()
     */
    function getNTPConf($mode = 'ntp.conf', $format = 'conf', $pooler = 0, $poolers = 0)
    {
        if (!$ntpconf = APICache::read('ntp.conf~'.md5(API_ZONE_DOMAIN) . "pool.$pooler.of.$poolers")) {
            
            $properties = $datas = $hostnames = $authors = $links = $pools = $servers = array();
            if ($pooler <= $poolers && $pooler > 0) {
                list($count) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF("SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . '` WHERE `typal` = "pool" AND  `online` > `offline` AND `uptime` > `downtime` ORDER BY `pinging` ASC'));
                $limit = floor($count / $poolers);
                $result = $GLOBALS['APIDB']->queryF("SELECT DISTINCT `typal`, `hostname`, `port`, `pinging`, `companyname`, `companyurl`, `properties`, `data` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . '` WHERE `typal` = "pool" AND  `online` > `offline` AND `uptime` > `downtime` ORDER BY `pinging` ASC LIMIT ' . ($limit * ($pooler-1)) . ','.$limit);
            } else {         
                list($count) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF("SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . '` WHERE `typal` = "pool" AND  `online` > `offline` AND `uptime` > `downtime` ORDER BY `pinging` ASC'));
                $result = $GLOBALS['APIDB']->queryF("SELECT DISTINCT `typal`, `hostname`, `port`, `pinging`, `companyname`, `companyurl`, `properties`, `data` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . '` WHERE `typal` = "pool" AND  `online` > `offline` AND `uptime` > `downtime` ORDER BY RAND() ASC LIMIT ' . mt_rand(floor($count * (3/5)), $count));
            }
     
            while($row = $GLOBALS['APIDB']->fetchArray($result)) {
                $properties = json_decode($row['properties'], true);
                $datas = json_decode($row['data'], true);
                if (!empty($row['hostname'])) {
                    $hostname = $row['hostname'];
                    $hostnames[] = $hostname;
                    if (!in_array(API_ZONE_DOMAIN, $properties['sites'][$hostname])) {
                        $properties['sites'][$hostname][API_ZONE_DOMAIN] = API_ZONE_DOMAIN;
                        if (!$pooling = APICache::read(API_ZONE_DOMAINKEY.$mode.'-pooling'))
                            $pooling = array();
                        
                        if (!in_array($hostname, array_keys($pooling))) {
                            
                            $authkey = json_decode(getURIData(str_replace('%apiurl', API_ZONE_URL, API_ZONE_AUTHKEY) . '?' . http_build_query(array('username' => API_ZONE_USERNAME, 'password' => API_ZONE_PASSWORD, 'format' => 'json')), 7, 11, array()), true);
                            $domains = json_decode(getURIData((str_replace('%apiurl', API_ZONE_URL, str_replace('%authkey', $authkey['authkey'], API_ZONE_DOMAINKEYS))), 7, 11, array()), true);
                            if (isset($domains['domains']) && is_array($domains['domains']))
                                foreach($domains['domains'] as $domain) {
                                    if ($domain['name'] == API_ZONE_DOMAIN || $domain['master'] == API_ZONE_DOMAIN) {
                                        if (!defined("API_ZONE_DOMAINKEY"))
                                            define("API_ZONE_DOMAINKEY", $domain['domainkey']);
                                    }
                                }
                            $records = json_decode(getURIData(str_replace('%apiurl', API_ZONE_URL, str_replace('%authkey', $authkey['authkey'], str_replace('%domainkey', API_ZONE_DOMAINKEY, API_ZONE_DNSRECORDIRECTORY_SEPARATOR))), 7, 11, array()), true);
                            
                            $pooling[$hostname]['hostname'] = $hostname;
                            $pooling[$hostname]['alias'] = sprintf(API_ZONE_SUBDOMAIN, hash('adler32', $hostname), $row['typal']);
                            $pooling[$hostname]['alias-wildcard'] = '*.'.sprintf(API_ZONE_SUBDOMAIN, hash('adler32', $hostname), $row['typal']);
                            $datas['typal'][API_ZONE_DOMAIN][$hostname] = $pooling[$hostname]['typal'] = $row['typal'];
                            
                            if (!in_array($hostname, array_keys($datas['alias'][API_ZONE_DOMAIN]))) {
                                $datas['alias'][API_ZONE_DOMAIN][$hostname] = $pooling[$hostname]['alias'];   
                                if (!APICache::read($pooling[$hostname]['alias'].'-'.$pooling[$hostname]['hostname'])) {
                                    if (isset($records['zones']) && is_array($records['zones']))
                                        foreach($records['zones'] as $record) {
                                            if ($record['name'] == $pooling[$hostname]['alias'] && $record['type'] == API_ZONE_CNAMETYPE) {
                                                if ($record['content'] != $hostname) {
                                                    @getURIData((str_replace('%apiurl', API_ZONE_URL, str_replace('%authkey', $authkey['authkey'], str_replace('%recordkey', $record['recordkey'], API_ZONE_EDITRECORD))) . '?' . http_build_query(array('content'=>$hostname))), 7, 11, array());
                                                }
                                                $apiaction = true;
                                                APICache::write($pooling[$hostname]['alias'].'-'.$pooling[$hostname]['hostname'], array('time'=>time()), time());
                                            }
                                        }
                                    if (isset($hostname) && !empty($hostname) && $apiaction!=true) {
                                        @getURIData((str_replace('%apiurl', API_ZONE_URL, str_replace('%authkey', $authkey['authkey'], API_ZONE_ADDRECORD)) .'?'. http_build_query(array('domain' => API_ZONE_DOMAINKEY, 'type' => API_ZONE_CNAMETYPE, 'name' => sprintf(API_ZONE_SUBDOMAIN, hash('adler32', $hostname), $row['typal']), 'content'=>$hostname, 'ttl' => 6000, 'prio' => 5, 'format' => 'json'))), 7, 11, array());
                                        APICache::write($pooling[$hostname]['alias'].'-'.$pooling[$hostname]['hostname'], array('time'=>time()), time());
                                        $apiaction = true;
                                    }
                                    APICache::write($pooling[$hostname]['alias'].'-'.$pooling[$hostname]['hostname'], array('time'=>time()), time());
                                }
                            }
                            if (!in_array($hostname, array_keys($datas['alias-wildcard'][API_ZONE_DOMAIN]))) {
                                $datas['alias-wildcard'][API_ZONE_DOMAIN][$hostname] = $pooling[$hostname]['alias-wildcard'];  
                                if (!APICache::read('uno.'.$pooling[$hostname]['alias'].'-'.$pooling[$hostname]['alias'])) {
                                    $apiactionb = false;
                                    if (isset($records['zones']) && is_array($records['zones']))
                                        foreach($records['zones'] as $record) {
                                            if ($record['name'] == $pooling[$hostname]['alias-wildcard'] && $record['type'] == API_ZONE_CNAMETYPE) {
                                                if ($record['content'] != sprintf(API_ZONE_SUBDOMAIN, hash('adler32', $hostname), $row['typal'])) {
                                                    @getURIData((str_replace('%apiurl', API_ZONE_URL, str_replace('%authkey', $authkey['authkey'], str_replace('%recordkey', $record['recordkey'], API_ZONE_EDITRECORD))) . '?' . http_build_query(array('content'=>sprintf(API_ZONE_SUBDOMAIN, hash('adler32', $hostname), $row['typal'])))), 7, 11, array());
                                                }
                                                $apiactionb = true;
                                                APICache::write('uno.'.$pooling[$hostname]['alias'].'-'.$pooling[$hostname]['alias'], array('time'=>time()), time());
                                            }
                                        }
                                    if ($apiactionb!=true) {
                                        @getURIData((str_replace('%apiurl', API_ZONE_URL, str_replace('%authkey', $authkey['authkey'], API_ZONE_ADDRECORD)) .'?'. http_build_query(array('domain' => API_ZONE_DOMAINKEY, 'type' => API_ZONE_CNAMETYPE, 'name' => sprintf(API_ZONE_SUBDOMAIN, '*.' . hash('adler32', $hostname), $row['typal']), 'content'=>sprintf(API_ZONE_SUBDOMAIN, hash('adler32', $hostname), $row['typal']), 'ttl' => 6000, 'prio' => 5, 'format' => 'json'))), 7, 11, array());
                                        $apiactionb = true;
                                        APICache::write('uno.'.$pooling[$hostname]['alias'].'-'.$pooling[$hostname]['alias'], array('time'=>time()), time());
                                    }
                                    APICache::write('uno.'.$pooling[$hostname]['alias'].'-'.$pooling[$hostname]['alias'], array('time'=>time()), time());
                                }
                            }
                            APICache::write(API_ZONE_DOMAINKEY.$mode.'-pooling', $pooling, time());
                        }
                        $GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` SET `properties` = '" . $GLOBALS['APIDB']->escape(json_encode($properties)) . "' AND `data` = '" . $GLOBALS['APIDB']->escape(json_encode($datas)) . "' WHERE `hostname` LIKE '" . $row['hostname'] . "' AND `port` = '" . $row['port'] . "'");
                        
                    }
                    if (!isset($pools[$row['hostname']]) && $row['port'] == '123')
                        $pools[$row['hostname']] = sprintf("pool %s iburst\t\t\t## %s <%s>", $datas['alias'][API_ZONE_DOMAIN][$hostname], $row['companyname'], $row['hostname'].":".$row['port']);
                    elseif (!isset($pools[$row['hostname']]) && $row['port'] != '123')
                        $pools[$row['hostname'].":".$row['port']] = sprintf("pool %s:%s iburst\t\t\t## %s <%s>", $datas['alias'][API_ZONE_DOMAIN][$hostname], $row['port'], $row['companyname'], $row['hostname'].":".$row['port']);
                }
            }
            if (count($pooling) > 0)
                APICache::write(API_ZONE_DOMAINKEY.$mode.'-pooling', $pooling, time());
        
            $result = $GLOBALS['APIDB']->queryF("SELECT DISTINCT `typal`, `hostname`, `port`, `pinging`, `companyname`, `companyurl`, `properties`, `data` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . '` WHERE `typal` = "server" AND  `online` > `offline` AND `uptime` > `downtime` ORDER BY `pinging` ASC');
            while($row = $GLOBALS['APIDB']->fetchArray($result)) {
                $properties = json_decode($row['properties'], true);
                $datas = json_decode($row['data'], true);
                if (!empty($row['hostname'])) {
                    $hostname = $row['hostname'];
                    $hostnames[] = $hostname;
                    if (!in_array(API_ZONE_DOMAIN, $properties['sites'][$hostname])) {
                        $properties['sites'][$hostname][API_ZONE_DOMAIN] = API_ZONE_DOMAIN;
                        if (!$pooling = APICache::read(API_ZONE_DOMAINKEY.$mode.'-pooling'))
                            $pooling = array();
                            
                        if (!in_array($hostname, array_keys($pooling))) {
                            
                            $authkey = json_decode(getURIData(str_replace('%apiurl', API_ZONE_URL, API_ZONE_AUTHKEY) . '?' . http_build_query(array('username' => API_ZONE_USERNAME, 'password' => API_ZONE_PASSWORD, 'format' => 'json')), 7, 11, array()), true);
                            $domains = json_decode(getURIData((str_replace('%apiurl', API_ZONE_URL, str_replace('%authkey', $authkey['authkey'], API_ZONE_DOMAINKEYS))), 7, 11, array()), true);
                            if (isset($domains['domains']) && is_array($domains['domains']))
                                foreach($domains['domains'] as $domain) {
                                    if ($domain['name'] == API_ZONE_DOMAIN || $domain['master'] == API_ZONE_DOMAIN) {
                                        if (!defined("API_ZONE_DOMAINKEY"))
                                            define("API_ZONE_DOMAINKEY", $domain['domainkey']);
                                    }
                                }
                            $records = json_decode(getURIData(str_replace('%apiurl', API_ZONE_URL, str_replace('%authkey', $authkey['authkey'], str_replace('%domainkey', API_ZONE_DOMAINKEY, API_ZONE_DNSRECORDIRECTORY_SEPARATOR))), 7, 11, array()), true);
                            
                            $pooling[$hostname]['hostname'] = $hostname;
                            $pooling[$hostname]['alias'] = sprintf(API_ZONE_SUBDOMAIN, hash('adler32', $hostname), $row['typal']);
                            $pooling[$hostname]['alias-wildcard'] = '*.'.sprintf(API_ZONE_SUBDOMAIN, hash('adler32', $hostname), $row['typal']);
                            $datas['typal'][API_ZONE_DOMAIN][$hostname] = $pooling[$hostname]['typal'] = $row['typal'];
                            
                            if (!in_array($hostname, array_keys($datas['alias'][API_ZONE_DOMAIN]))) {
                                $datas['alias'][API_ZONE_DOMAIN][$hostname] = $pooling[$hostname]['alias'];
                                if (!APICache::read($pooling[$hostname]['alias'].'-'.$pooling[$hostname]['hostname'])) {
                                    if (isset($records['zones']) && is_array($records['zones']))
                                        foreach($records['zones'] as $record) {
                                            if ($record['name'] == $pooling[$hostname]['alias'] && $record['type'] == API_ZONE_CNAMETYPE) {
                                                if ($record['content'] != $hostname) {
                                                    @getURIData((str_replace('%apiurl', API_ZONE_URL, str_replace('%authkey', $authkey['authkey'], str_replace('%recordkey', $record['recordkey'], API_ZONE_EDITRECORD))) . '?' . http_build_query(array('content'=>$hostname))));
                                                }
                                                $apiaction = true;
                                                APICache::write($pooling[$hostname]['alias'].'-'.$pooling[$hostname]['hostname'], array('time'=>time()), time());
                                            }
                                        }
                                    if (isset($hostname) && !empty($hostname) && $apiaction!=true) {
                                        @getURIData((str_replace('%apiurl', API_ZONE_URL, str_replace('%authkey', $authkey['authkey'], API_ZONE_ADDRECORD)) .'?'. http_build_query(array('domain' => API_ZONE_DOMAINKEY, 'type' => API_ZONE_CNAMETYPE, 'name' => sprintf(API_ZONE_SUBDOMAIN, hash('adler32', $hostname), $row['typal']), 'content'=>$hostname, 'ttl' => 6000, 'prio' => 5, 'format' => 'json'))));
                                        APICache::write($pooling[$hostname]['alias'].'-'.$pooling[$hostname]['hostname'], array('time'=>time()), time());
                                        $apiaction = true;
                                    }
                                    APICache::write($pooling[$hostname]['alias'].'-'.$pooling[$hostname]['hostname'], array('time'=>time()), time());
                                }
                            }
                            
                            if (!in_array($hostname, array_keys($datas['alias-wildcard'][API_ZONE_DOMAIN]))) {
                                $datas['alias-wildcard'][API_ZONE_DOMAIN][$hostname] = $pooling[$hostname]['alias-wildcard'];
                                if (!APICache::read('uno.'.$pooling[$hostname]['alias'].'-'.$pooling[$hostname]['alias'])) {
                                    $apiactionb = false;
                                    if (isset($records['zones']) && is_array($records['zones']))
                                        foreach($records['zones'] as $record) {
                                            if ($record['name'] == $pooling[$hostname]['alias-wildcard'] && $record['type'] == API_ZONE_CNAMETYPE) {
                                                if ($record['content'] != sprintf(API_ZONE_SUBDOMAIN, hash('adler32', $hostname), $row['typal'])) {
                                                    @getURIData((str_replace('%apiurl', API_ZONE_URL, str_replace('%authkey', $authkey['authkey'], str_replace('%recordkey', $record['recordkey'], API_ZONE_EDITRECORD))) . '?' . http_build_query(array('content'=>sprintf(API_ZONE_SUBDOMAIN, hash('adler32', $hostname), $row['typal'])))));
                                                }
                                                $apiactionb = true;
                                                APICache::write('uno.'.$pooling[$hostname]['alias'].'-'.$pooling[$hostname]['alias'], array('time'=>time()), time());
                                            }
                                        }
                                    if ($apiactionb!=true) {
                                        @getURIData((str_replace('%apiurl', API_ZONE_URL, str_replace('%authkey', $authkey['authkey'], API_ZONE_ADDRECORD)) .'?'. http_build_query(array('domain' => API_ZONE_DOMAINKEY, 'type' => API_ZONE_CNAMETYPE, 'name' => sprintf(API_ZONE_SUBDOMAIN, '*.' . hash('adler32', $hostname), $row['typal']), 'content'=>sprintf(API_ZONE_SUBDOMAIN, hash('adler32', $hostname), $row['typal']), 'ttl' => 6000, 'prio' => 5, 'format' => 'json'))));
                                        $apiactionb = true;
                                        APICache::write('uno.'.$pooling[$hostname]['alias'].'-'.$pooling[$hostname]['alias'], array('time'=>time()), time());
                                    }
                                    APICache::write('uno.'.$pooling[$hostname]['alias'].'-'.$pooling[$hostname]['alias'], array('time'=>time()), time());
                                }
                                $GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` SET `properties` = '" . $GLOBALS['APIDB']->escape(json_encode($properties)) . "' AND `data` = '" . $GLOBALS['APIDB']->escape(json_encode($datas)) . "' WHERE `hostname` LIKE '" . $row['hostname'] . "' AND `port` = '" . $row['port'] . "'");
                            }
                            APICache::write(API_ZONE_DOMAINKEY.$mode.'-pooling', $pooling, time());
                        }
                    }
                    if (!isset($servers[$row['hostname']]) && $row['port'] == '123')
                        $servers[$row['hostname']] = sprintf("server %s\t\t\t## %s <%s>", $datas['alias'][API_ZONE_DOMAIN][$hostname], $row['companyname'], $pooling[$hostname]['hostname'].":".$row['port']);
                    elseif (!isset($pools[$row['hostname']]) && $row['port'] != '123')
                        $servers[$row['hostname'].":".$row['port']] = sprintf("server %s:%s\t\t\t## %s <%s>", $datas['alias'][API_ZONE_DOMAIN][$hostname], $row['port'], $row['companyname'], $pooling[$hostname]['hostname'].":".$row['port']);
                        
                }
            }
            
            $result = $GLOBALS['APIDB']->queryF($sql = "SELECT DISTINCT `typal`, `name`, `nameemail`, `pinging` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . '` WHERE `online` > `offline` AND `uptime` > `downtime` AND `hostname` IN ("' . implode('", "', $hostnames) . '") ORDER BY `pinging` ASC');
            while($row = $GLOBALS['APIDB']->fetchArray($result)) 
                if (!empty($row['name']) && !empty($row['nameemail']))
                    if (!isset($authors[$row['nameemail']]))
                        $authors[$row['nameemail']] = sprintf("## @author          %s <%s>", $row['name'], $row['nameemail']);
                        
            $result = $GLOBALS['APIDB']->queryF("SELECT DISTINCT `companyname`, `companyemail`, `pinging` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . '` WHERE  `online` > `offline` AND `uptime` > `downtime`  AND `hostname` IN ("' . implode('", "', $hostnames) . '") ORDER BY `pinging` ASC');
            while($row = $GLOBALS['APIDB']->fetchArray($result))
                if (!empty($row['companyname']) && !empty($row['companyemail']))
                    if (!isset($authors[$row['companyemail']]))
                        $authors[$row['companyemail']] = sprintf("## @author          %s <%s>", $row['companyname'], $row['companyemail']);
            
            $result = $GLOBALS['APIDB']->queryF("SELECT DISTINCT `typal`, `name`, `nameurl`, `pinging` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . '` WHERE  `online` > `offline` AND `uptime` > `downtime`  AND `hostname` IN ("' . implode('", "', $hostnames) . '") ORDER BY `pinging` ASC');
            while($row = $GLOBALS['APIDB']->fetchArray($result))
                if (!empty($row['name']) && !empty($row['nameurl']))
                    if (!isset($links[$row['nameurl']]))
                        $links[$row['nameurl']] = sprintf("## @link            %s %s", $row['name'], $row['nameurl']);
            
            $result = $GLOBALS['APIDB']->queryF("SELECT DISTINCT `typal`, `companyname`, `companyurl`, `pinging` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . '` WHERE  `online` > `offline` AND `uptime` > `downtime`  AND `hostname` IN ("' . implode('", "', $hostnames) . '") ORDER BY `pinging` ASC');
            while($row = $GLOBALS['APIDB']->fetchArray($result))
                if (!empty($row['companyname']) && !empty($row['companyurl']))
                    if (!isset($links[$row['companyurl']]))
                        $links[$row['companyurl']] = sprintf("## @link            %s %s", $row['companyname'], $row['companyurl']);
            
            if (count($authors)==0)
                $authors['##'] = '## ';
            
            if (count($links)==0)
                $links['##'] = '## ';
            $ntpconf = explode("\n", str_replace('\n\n## ', '\n## ', str_replace('%ip', $_SERVER['REMOTE_ADDR'], str_replace("%url", API_URL . $_SERVER['REQUEST_URI'], str_replace("YYYY/MM/DD HH:II:SS", date("Y-m-d W.Y, D, H:i:s"), str_replace('%servers', implode("\n", $servers), str_replace('%pools', implode("\n", $pools), str_replace('%links', implode("\n", $links), str_replace('%authors', implode("\n", $authors), file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'ntp.conf.txt'))))))))));
            APICache::write('ntp.conf~'.md5(API_ZONE_DOMAIN) . "pool.$pooler.of.$poolers", $ntpconf, mt_rand(141, 399) * mt_rand(1353, 2899));
            if (count($pooling)>0)
                APICache::write(API_ZONE_DOMAINKEY.$mode.'-pooling', $pooling, time());
         
        }
        header("Context-Type: text");
        die(implode("\n", $ntpconf));
    
    }
}

if (!class_exists("XmlDomConstruct")) {
	/**
	 * class XmlDomConstruct
	 * 
	 * 	Extends the DOMDocument to implement personal (utility) methods.
	 *
	 * @author 		Simon Roberts (Chronolabs) simon@labs.coop
	 */
	class XmlDomConstruct extends DOMDocument {
	
		/**
		 * Constructs elements and texts from an array or string.
		 * The array can contain an element's name in the index part
		 * and an element's text in the value part.
		 *
		 * It can also creates an xml with the same element tagName on the same
		 * level.
		 *
		 * ex:
		 * <nodes>
		 *   <node>text</node>
		 *   <node>
		 *     <field>hello</field>
		 *     <field>world</field>
		 *   </node>
		 * </nodes>
		 *
		 * Array should then look like:
		 *
		 * Array (
		 *   "nodes" => Array (
		 *     "node" => Array (
		 *       0 => "text"
		 *       1 => Array (
		 *         "field" => Array (
		 *           0 => "hello"
		 *           1 => "world"
		 *         )
		 *       )
		 *     )
		 *   )
		 * )
		 *
		 * @param mixed $mixed An array or string.
		 *
		 * @param DOMElement[optional] $domElement Then element
		 * from where the array will be construct to.
		 * 
		 * @author 		Simon Roberts (Chronolabs) simon@labs.coop
		 *
		 */
		public function fromMixed($mixed, DOMElement $domElement = null) {
	
			$domElement = is_null($domElement) ? $this : $domElement;
	
			if (is_array($mixed)) {
				foreach( $mixed as $index => $mixedElement ) {
	
					if ( is_int($index) ) {
						if ( $index == 0 ) {
							$node = $domElement;
						} else {
							$node = $this->createElement($domElement->tagName);
							$domElement->parentNode->appendChild($node);
						}
					}
					 
					else {
						$node = $this->createElement($index);
						$domElement->appendChild($node);
					}
					 
					$this->fromMixed($mixedElement, $node);
					 
				}
			} else {
				$domElement->appendChild($this->createTextNode($mixed));
			}
			 
		}
		 
	}
}

function api_load($name, $type = 'core')
{
    if (!class_exists('XoopsLoad')) {
        require_once API_ROOT_PATH . '/class/apiload.php';
    }
    
    return APILoad::load($name, $type);
}


function getHTMLForm($mode = '', $var = '')
{
    require_once dirname(__DIR__) . DS . 'class' . DS . 'apiformloader.php';
    
    $form = array();
    switch ($mode)
    {
        case "subscribe":
            $form[] = "<form name='upload-aliases' method=\"POST\" enctype=\"multipart/form-data\" action=\"./uploading.php\">";
            $form[] = "\t<table class='upload-aliases' id='auth-key' style='vertical-align: top !important; min-width: 98%;'>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 320px;'>";
            $form[] = "\t\t\t\t<label for='list'>CSV List of Aliases:</label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='list' id='list' size='255' value='@lists.sourceforge.net' />";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 320px;'>";
            $form[] = "\t\t\t\t<label for='subject'>Email Subject:</label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='subject' id='subject' size='255' value='subscribe'/>&nbsp;&nbsp;";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 320px;'>";
            $form[] = "\t\t\t\t<label for='filename'>CSV List of Aliases:</label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='file' name='filename' id='filename' size='21' />&nbsp;&nbsp;";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: auto; background-color: #feedcc; padding: 10px;' colspan='2'>";
            $form[] = "\t\t\t\tThe CSV must be a standard excel or linux format and have the four captioned top row fields of: Name, Email, Alias, Domain!<br/><br/>There is two example spreedsheets with the titles in place you can populate you can download these from: <a href='" . API_URL . "/assets/docs/csv-prop-spreedsheet.xlsx' target='_blank'>csv-prop-spreedsheet.xlsx</a> or <a href='" . API_URL . "/assets/docs/csv-prop-spreedsheet.ods' target='_blank'>csv-prop-spreedsheet.ods</a>; thanks for using the example spreedsheets to generate the correct titled CSV in the right formating!";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<label for='format'>Output Format:</label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td style='width: 320px;'>";
            $form[] = "\t\t\t\t<select name='format' id='format'/>";
            $form[] = "\t\t\t\t\t<option value='raw'>RAW PHP Output</option>";
            $form[] = "\t\t\t\t\t<option value='json' selected='selected'>JSON Output</option>";
            $form[] = "\t\t\t\t\t<option value='serial'>Serialisation Output</option>";
            $form[] = "\t\t\t\t\t<option value='xml'>XML Output</option>";
            $form[] = "\t\t\t\t</select>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td colspan='3' style='padding-left:64px;'>";
            $form[] = "\t\t\t\t<input type='hidden' value='".$authkey."' name='authkey'>";
            $form[] = "\t\t\t\t<input type='hidden' value='subscribe' name='mode'>";
            $form[] = "\t\t\t\t<input type='submit' value='Upload *.csv and propogate email aliases!' name='submit' style='padding:11px; font-size:122%;'>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td colspan='3' style='padding-top: 8px; padding-bottom: 14px; padding-right:35px; text-align: right;'>";
            $form[] = "\t\t\t\t<font style='color: rgb(250,0,0); font-size: 139%; font-weight: bold;'>* </font><font  style='color: rgb(10,10,10); font-size: 99%; font-weight: bold'><em style='font-size: 76%'>~ Required Field for Form Submission</em></font>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t</table>";
            $form[] = "</form>";
            break;
        case "addntp":
            error_reporting(E_ALL);
            ini_set('display_errors', true);
            api_load('APIForm');
            $apiform = new APIThemeForm('Add New NTP Network Source', 'addntp', API_URL . '/v1/addntp.api', 'post', false, 'This form is used for creating a new entry in the NTP Services Pools Database - A Telephanist is someone that is used for a template like DJ on Reports Coming From this Service - you can create a forwarder for company name and telephanist@ntp.example.com!');
            $objform = array();
            $objform['hostname']['obj'] = new APIFormText("Hostname/IP Address:", "hostname", 28, 250);
            $objform['hostname']['description'] = ("NTP Service IP Address or Hostname (Will Test!)");
            $objform['hostname']['required'] = true;
            $objform['port']['obj'] = new APIFormText("Port:", "port", 8, 5);
            $objform['port']['description'] = ("NTP Service Port (Normally 123, 124, 125)");
            $objform['port']['required'] = true;
            $objform['name']['obj'] = new APIFormText("Telephantist Name:", "name", 28, 128);
            $objform['name']['description'] = ("This is who manages and reporting telephany is based on the record!");
            $objform['name']['required'] = true;
            $objform['nameurl']['obj'] = new APIFormText("Telephantist URL:", "nameurl", 28, 128);
            $objform['nameurl']['description'] = ("This is who manages and reporting telephany is based on blog or information site URL for the record!");
            $objform['nameurl']['required'] = false;
            $objform['nameemail']['obj'] = new APIFormText("Telephantist eMail:", "nameemail", 28, 196);
            $objform['nameemail']['description'] = ("This is who manages and reporting telephany is based on the record email address, will recieve no reporting emails!");
            $objform['nameemail']['required'] = true;
            $objform['companyname']['obj'] = new APIFormText("Company's Name:", "companyname", 28, 128);
            $objform['companyname']['description'] = ("Company/Organisation/Facility name not a url or abbrivation complete name only!");
            $objform['companyname']['required'] = true;
            $objform['companyemail']['obj'] = new APIFormText("Company's eMail:", "companyemail", 28, 196);
            $objform['companyemail']['description'] = ("Company/Organisation/Facility email for NTP Services; indirect reporting will be sent to this address should goto who manages the NTP Services!");
            $objform['companyemail']['required'] = true;
            $objform['companyrbn']['obj'] = new APIFormText("Company's Register Number:", "companyrbn", 28, 196);
            $objform['companyrbn']['description'] = ("Business/Facility Rego Number or simply Postcode for a location postcode or zip for the rego number!");
            $objform['companyrbn']['required'] = true;
            $objform['companyrbntype']['obj'] = new APIFormText("Company's Register Number Type:", "companyrbntype", 8, 16);
            $objform['companyrbntype']['description'] = ("This is the TLA name of the Business Rego Number or simply Postcode for a location postcode or zip for the rego number!");
            $objform['companyrbntype']['required'] = true;
            $objform['companyurl']['obj'] = new APIFormText("Company URL:", "companyurl", 28, 128);
            $objform['companyurl']['required'] = false;
            $objform['format']['obj'] = new APIFormSelect("Output Format::", "format", 4, 4);
            $objform['format']['required'] = true;
            $objform['format']['obj']->addOption('raw', 'RAW PHP Output');
            $objform['format']['obj']->addOption('json', 'JSON Output');
            $objform['format']['obj']->addOption('serial', 'Serialisation Output');
            $objform['format']['obj']->addOption('xml', 'XML Output');
            $objform['mode']['obj'] = new APIFormHidden("mode", "addntp");
            $objform['mode']['required'] = false;
            $objform['submit']['obj'] = new APIFormButton("Add NTP Source to DB", "submit", 'submit');
            $objform['submit']['required'] = false;
            foreach($objform as $key => $obj) {
                if (isset($objform[$key]['description']))
                    $objform[$key]['obj']->setDescription($objform[$key]['description']);
                $apiform->addElement($objform[$key]['obj'], $objform[$key]['required']);
            }
            return $apiform->render();
            break;
            
        case "editntp":
            error_reporting(E_ALL);
            ini_set('display_errors', true);
            api_load('APIForm');
            $apiform = new APIThemeForm('Add New NTP Network Source', 'editntp', API_URL . '/v1/'.$var['key'].'/editntp.api', 'post', false, 'This form is used for creating a new entry in the NTP Services Pools Database - A Telephanist is someone that is used for a template like DJ on Reports Coming From this Service - you can create a forwarder for company name and telephanist@ntp.example.com!');
            $objform = array();
            $objform['hostname']['obj'] = new APIFormText("Hostname/IP Address:", "hostname", 28, 250, $var['hostname']);
            $objform['hostname']['required'] = true;
            $objform['port']['obj'] = new APIFormText("Port:", "port", 8, 5, $var['port']);
            $objform['port']['required'] = true;
            $objform['name']['obj'] = new APIFormText("Telephantist Name:", "name", 28, 128, $var['name']);
            $objform['name']['required'] = true;
            $objform['nameurl']['obj'] = new APIFormText("Telephantist URL:", "nameurl", 28, 128, $var['nameurl']);
            $objform['nameurl']['required'] = false;
            $objform['nameemail']['obj'] = new APIFormText("Telephantist eMail:", "nameemail", 28, 196, $var['nameemail']);
            $objform['nameemail']['required'] = true;
            $objform['companyname']['obj'] = new APIFormText("Company's Name:", "companyname", 28, 128, $var['companyname']);
            $objform['companyname']['required'] = true;
            $objform['companyemail']['obj'] = new APIFormText("Company's eMail:", "companyemail", 28, 196, $var['companyrbn']);
            $objform['companyemail']['required'] = true;
            $objform['companyrbn']['obj'] = new APIFormText("Company's Register Number:", "companyrbn", 28, 196, $var['companyrbn']);
            $objform['companyrbn']['obj']->setDescription("Business/Facility Rego Number or simply Postcode for a location postcode or zip for the rego number!");
            $objform['companyrbn']['required'] = true;
            $objform['companyrbntype']['obj'] = new APIFormText("Company's Register Number Type:", "companyrbntype", 8, 16, $var['companyrbntype']);
            $objform['companyrbntype']['obj']->setDescription("This is the TLA name of the Business Rego Number or simply Postcode for a location postcode or zip for the rego number!");
            $objform['companyrbntype']['required'] = true;
            $objform['companyurl']['obj'] = new APIFormText("Company URL:", "companyurl", 28, 128, $var['companyurl']);
            $objform['companyurl']['required'] = false;
            $objform['format']['obj'] = new APIFormSelect("Output Format::", "format", 4, 4);
            $objform['format']['required'] = true;
            $objform['format']['obj']->addOption('raw', 'RAW PHP Output');
            $objform['format']['obj']->addOption('json', 'JSON Output');
            $objform['format']['obj']->addOption('serial', 'Serialisation Output');
            $objform['format']['obj']->addOption('xml', 'XML Output');
            $objform['mode']['obj'] = new APIFormHidden("mode", "editntp");
            $objform['mode']['required'] = false;
            $objform['mode']['obj'] = new APIFormHidden("key", $var['key']);
            $objform['mode']['required'] = false;
            $objform['submit']['obj'] = new APIFormButton("Edit NTP from Bucky Record to Assigned - Only Happens Once!", "submit", 'submit');
            $objform['submit']['required'] = false;
            foreach($objform as $key => $obj)
                $apiform->addElement($objform[$key]['obj'], $objform[$key]['required']);
                return $apiform->render();
            break;
            
                    
    }
    return implode("\n", $form);

}


if (!function_exists("getBaseDomain")) {
    /**
     * Gets the base domain of a tld with subdomains, that is the root domain header for the network rout
     *
     * @param string $url
     *
     * @return string
     */
    function getBaseDomain($uri = '')
    {
        
        static $fallout, $strata, $classes;

        if (empty($classes))
        {
            
            $attempts = 0;
            $attempts++;
            $classes = array_keys(json_decode(getURIData(API_STRATA_API_URL ."/v1/strata/json.api", 150, 100), true));
            
        }
        if (empty($fallout))
        {
            $fallout = array_keys(json_decode(getURIData(API_STRATA_API_URL ."/v1/fallout/json.api", 150, 100), true));
        }
        
        // Get Full Hostname
        $uri = strtolower($uri);
        $hostname = parse_url($uri, PHP_URL_HOST);
        if (!filter_var($hostname, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 || FILTER_FLAG_IPV4) === false)
            return $hostname;
        
        // break up domain, reverse
        $elements = explode('.', $hostname);
        $elements = array_reverse($elements);
        
        // Returns Base Domain
        if (in_array($elements[0], $classes))
            return $elements[1] . '.' . $elements[0];
        elseif (in_array($elements[0], $fallout) && in_array($elements[1], $classes))
            return $elements[2] . '.' . $elements[1] . '.' . $elements[0];
        elseif (in_array($elements[0], $fallout))
            return  $elements[1] . '.' . $elements[0];
        else
            return  $elements[1] . '.' . $elements[0];
    }
}
