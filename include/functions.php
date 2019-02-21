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
        if (validateDomain($hostname)) {
            if (validateEmail($nameemail) || (!validateEmail($nameemail) && !validateEmail($companyemail))) {
                if (validateEmail($companyemail) || (!validateEmail($nameemail) && !validateEmail($companyemail))) {
                    $sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` (`typal`, `state`, `hostname`, `port`, `name`, `nameemail`, `nameurl`, `companyname`, `companyemail`, `companyrbn`, `companyrbntype`, `companytype`, `companyurl`) VALUES('pool', 'bucky', '" . $GLOBALS['APIDB']->escape($hostname) . "', '" . $GLOBALS['APIDB']->escape($port) . "', '" . $GLOBALS['APIDB']->escape($name) . "', '" . $GLOBALS['APIDB']->escape($nameemail) . "', '" . $GLOBALS['APIDB']->escape($nameurl) . "', '" . $GLOBALS['APIDB']->escape($companyname) . "', '" . $GLOBALS['APIDB']->escape($companyemail) . "', '" . $GLOBALS['APIDB']->escape($companyrbn) . "', '" . $GLOBALS['APIDB']->escape($companyrbntype) . "', '" . $GLOBALS['APIDB']->escape($companytype) . "', '" . $GLOBALS['APIDB']->escape($companyurl) . "')";
                    if ($GLOBALS['APIDB']->queryF($sql)) {
                        return array('code' => 201, 'errors' => array(), 'key' => md5($GLOBALS['APIDB']->getInsertId().$nameemail.$companyemail.'ntpservice'.API_URL));
                    } else {
                        return array('code' => 501, 'errors' => array($GLOBALS['APIDB']->errno => "Error with SQL: $sql;"));
                    }
                } else {
                    return array('code' => 501, 'errors' => array(101 => 'Company Email doesn\'t conform to email standard!'));
                }
            } else {
                return array('code' => 501, 'errors' => array(102 => 'Telephanist Email doesn\'t conform to email standard!'));
            }
        } else {
            return array('code' => 501, 'errors' => array(103 => 'Hostname of NTP Service doesn\'t conform to netbios net addressing cname standard!'));
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
        $sql = "SELECT `id` WHERE 'state' = 'bucky' AND '$key' = md5(concat(`id`,`nameemail`,`companyemail`,'ntpservice','".API_URL."'))";
        list($id) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF($sql));
        if ($id <> 0) {
            if (getHostnameNTPPing($hostname, $port) > 0) {
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
                return array('code' => 501, 'errors' => array(101 => 'Hostname doesn\'t ping or pickup!'));
            }
        } else {
            return array('code' => 501, 'errors' => array(101 => 'Key doesn\'t match any records!'));
        }
        return false;
    }
}


if (!function_exists("getHostsKeys")) {
    
    function getHostsKeys($mode, $format) 
    {

        switch ($mode) {
            case 'online':
                $sql = "SELECT md5(concat(`id`,`nameemail`,`companyemail`,'ntpservice','".API_URL."')) as `key`, `hostname`, `port`, `companyname` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE `uptime` > `downtime` ORDER BY `uptime` DESC";
                break;
            case 'offline':
                $sql = "SELECT md5(concat(`id`,`nameemail`,`companyemail`,'ntpservice','".API_URL."')) as `key`, `hostname`, `port`, `companyname` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE `uptime` <= `downtime` ORDER BY `downtime` DESC";
                break;
        }
        
        $results = array();
        $result = $GLOBALS['APIDB']->queryF($sql);
        while($row = $GLOBALS['APIDB']->fetchArray($result))
        {
            if (!isset($results[str_replace('.', ' ', $row['hostname'])]))
                $results[str_replace('.', '-', $row['hostname'])] = $row;
        }
        return $results;
    }
}


if (!function_exists("getHostsLestatsKeys")) {
    
    function getHostsLestatsKeys($mode, $format) 
    {
        
        switch ($mode) {
            case 'pings':
                $sql = "SELECT md5(concat(`id`,`nameemail`,`companyemail`,'ntpservice','".API_URL."')) as `key`, `hostname`, `pinging` as `pinging-in-ms` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE `pinging` > 0 ORDER BY `pinging` ASC";
                break;
            case 'uptime':
                $sql = "SELECT md5(concat(`id`,`nameemail`,`companyemail`,'ntpservice','".API_URL."')) as `key`, `hostname`, `uptime` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE `uptime` > `downtime` AND `uptime` > 0  ORDER BY `uptime` DESC";
                break;
            case 'downtime':
                $sql = "SELECT md5(concat(`id`,`nameemail`,`companyemail`,'ntpservice','".API_URL."')) as `key`, `hostname`, `downtime` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE `uptime` <= `downtime` AND `downtime` > 0 ORDER BY `downtime` DESC";
                break;
            case 'nextping':
                $sql = "SELECT md5(concat(`id`,`nameemail`,`companyemail`,'ntpservice','".API_URL."')) as `key`, `hostname`, `pinged` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE `pinged` > UNIX_TIMESTAMP() ORDER BY `pinged` ASC";
                break;
        }
        
        $results = array();
        $result = $GLOBALS['APIDB']->queryF($sql);
        while($row = $GLOBALS['APIDB']->fetchArray($result))
        {
            if (isset($row['uptime']))
                $row['uptime'] = formatMSASTime($row['uptime'] * 1000);
            if (isset($row['downtime']))
                $row['downtime'] = formatMSASTime($row['downtime'] * 1000);
            if (isset($row['pinged']))
                $row['pinged'] = date("Y-m-d W, D, H:i:s", $row['pinged']);
            if (!isset($results[str_replace('.', ' ', $row['hostname'])]))
                $results[str_replace('.', '-', $row['hostname'])] = $row;
        }
        return $results;
    }
}


if (!function_exists("getHostsRSS")) {
    
    function getHostsRSS($mode, $items, $format) {
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
                $sql = "SELECT md5(concat(`id`,`nameemail`,`companyemail`,'ntpservice','".API_URL."')) as `key`, `pinging`, `pinged`, `uptime`, `downtime`, sha1(concat((`uptime` / `pinging` * 1000), `id`,`nameemail`,`companyemail`,'ntpservice','".API_URL."')) as `guid`, `hostname`, `port`, `name`, `nameurl`, `companyname`, `companyurl`, `online` as `pubDate` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE (`uptime` / `pinging` * 1000) > 0 HAVING (`pinged` > UNIX_TIMESTAMP() AND `pinging` > 0 AND `uptime` > 0) ORDER BY (`uptime` / `pinging` * 1000) DESC LIMIT $items";
                break;
            case 'worse':
                $feed['title'] = "Worse NTP Services on: " . API_URL;
                $feed['desc'] = "This is the worst NTP Services on: " . API_URL . " ~ they can variable and variate from time to time!";
                $sql = "SELECT md5(concat(`id`,`nameemail`,`companyemail`,'ntpservice','".API_URL."')) as `key`, `pinging`, `pinged`, `uptime`, `downtime`, sha1(concat((`downtime` * `pinging` / 1000), `id`,`nameemail`,`companyemail`,'ntpservice','".API_URL."')) as `guid`, `hostname`, `port`, `name`, `nameurl`, `companyname`, `companyurl`, `offline` as `pubDate` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE (`downtime` * `pinging` / 1000) > 0 HAVING (`pinged` > UNIX_TIMESTAMP() AND `pinging` > 0  AND `downtime` > 0) ORDER BY (`downtime` * `pinging` / 1000) DESC LIMIT $items";
                break;
            case 'new':
                $feed['title'] = "New NTP Services on: " . API_URL;
                $feed['desc'] = "This is the new NTP Services on: " . API_URL . " ~ they can variable and variate from time to time!";
                $sql = "SELECT md5(concat(`id`,`nameemail`,`companyemail`,'ntpservice','".API_URL."')) as `key`, `pinging`, `pinged`, `uptime`, `downtime`, sha1(concat(`created`, `id`,`nameemail`,`companyemail`,'ntpservice','".API_URL."')) as `guid`, `hostname`, `port`, `name`, `nameurl`, `companyname`, `companyurl`, UNIX_TIMESTAMP() as `pubDate` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` ORDER BY `created` DESC LIMIT $items";
                break;
        }
        
        $results = array();
        $result = $GLOBALS['APIDB']->queryF($sql);
        while($row = $GLOBALS['APIDB']->fetchArray($result))
        {
            $row['title'] = $row['companyname'] . ' ~ [ ' . $row['hostname'] . ":" . $row['port'] . ' ] :: [ ' . formatMSASTime($row['pinging']) . ' ]';
            if (isset($row['uptime']))
                $row['uptime'] = formatMSASTime($row['uptime'] * 1000);
            if (isset($row['downtime']))
                $row['downtime'] = formatMSASTime($row['downtime'] * 1000);
            if (isset($row['pinging']))
                $row['pinging'] = formatMSASTime($row['pinging']);
            if (isset($row['pinged']))
                $row['pinged'] = date("Y-m-d W, D, H:i:s", $row['pinged']);
            if (isset($row['pubDate']))
                $row['pubDate'] = formatRssTimestamp($row['pubDate']);
            if (!isset($results[str_replace('.', ' ', $row['hostname'])]))
                $results[str_replace('.', '-', $row['hostname'])] = $row;
        }
        if (count($results)) {
            $feeditem = file_get_contents(__DIR__ . DS . 'data' . DS . 'item.xml');
            $items = array();
            foreach($results as $key => $result) {
                $items[$key] = $feeditem;
                $keys = array_keys($result);
                sort($keys, SORT_DESC);
                foreach($keys as $field)
                    $items[$key] = str_replace("%$field", $result[$field], $items[$key]);
            }
            $feed['items'] = implode("\n\n", $items);
            $feedxml = file_get_contents(__DIR__ . DS . 'data' . DS . 'feed.xml');
            foreach($feed as $field => $value)
                $feedxml = str_replace("%$field", $value, $feedxml);
            header('Content-type: application/rss+xml');
            die($feedxml);
        }
        return false;
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
            curl_setopt($btt, CURLOPT_POSTFIELDS, http_build_query($post));
            
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
        $output = array();
        if (validateIPv6($hostname))
            exec('ping6 -c ' . mt_rand(7,19) . " $hostname", $output);
        else
            exec('ping -c ' . mt_rand(7,19) . " $hostname", $output);
        $ms = $num = 0;
        foreach($output as $line) {
            $parts = explode('time=', $line);
            if (isset($parts[1]) && !empty($parts[1])) {
                $parts = explode(' ', $parts[1]);
                if (isset($parts[0]) && is_numeric($parts[0]) && $parts[1] = 'ms') {
                    $ms = $ms + (integer)$parts[0];
                    $num++;
                } elseif (isset($parts[0]) && is_numeric($parts[0]) && $parts[1] = 's') {
                    $ms = $ms + (integer)$parts[0] / 60 * 1000;
                    $num++;
                }
            }
        }
        return $ms / $num;
    }
}

/**
 * Returns UNIX timestamp from a NTP server (RFC 5905)
 *
 * @param  string  $host    Server host (default is pool.ntp.org)
 * @param  integer $timeout Timeout  in seconds (default is 10 seconds)
 * @return integer Number of seconds since January 1st 1970
 */
function getTimeFromNTP($host = 'pool.ntp.org', $port = 123, $timeout = 27)
{
    $fp = fsockopen($host,$port,$err,$errstr,$timeout);
    # parameters: server, socket, error code, error text, timeout
    if($fp)
    {
        fputs($fp, "\n");
        $timevalue = fread($fp, 49);
        fclose($fp); # close the connection
    }
    else
    {
        $timevalue = " ";
    }
    if ($err <> 0)
        die("\n\n#$err - $errstr\n\n");
    return (float)$timevalue;
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
            die($time = getTimeFromNTP($hostname, $port));
            if ($time<>0) {
                $ms += (microtime(true) * 1000) - ($start * 1000);
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
        STATIC $return = array();
        
        if (isset($return[$milliseconds]))
            return $return[$milliseconds];
        
        $return[$milliseconds] = '';
        if (3600 * 24 * 7 > ($milliseconds / 1000 / 3600 / 24 / 7))
        {
            $scratch = ($milliseconds/1000/3600/24/7);
            $parts = explode(".", $scratch);
            $return[$milliseconds] .= $parts[0] . 'week' .($parts[0]>1?"s ":" ");
            $milliseconds = ((float)"0." . $parts[1]) * 1000 * 3600 * 24 * 7;
        }
        if (3600 * 24 * 1 > ($milliseconds / 1000 / 3600 / 24 / 1))
        {
            $scratch = ($milliseconds/1000/3600/24/1);
            $parts = explode(".", $scratch);
            $return[$milliseconds] .= $parts[0] . 'day' .($parts[0]>1?"s ":" ");
            $milliseconds = ((float)"0." . $parts[1]) * 1000 * 3600 * 24 * 1;
        }
        if (3600 > ($milliseconds / 1000 / 3600))
        {
            $scratch = ($milliseconds/1000/3600);
            $parts = explode(".", $scratch);
            $return[$milliseconds] .= $parts[0] . 'hour' .($parts[0]>1?"s ":" ");
            $milliseconds = ((float)"0." . $parts[1]) * 1000 * 3600 * 24 * 1;
        }
        if (60 > ($milliseconds / 1000 / 60))
        {
            $scratch = ($milliseconds/1000/60);
            $parts = explode(".", $scratch);
            $return[$milliseconds] .= $parts[0] . 'min' .($parts[0]>1?"s ":" ");
            $milliseconds = ((float)"0." . $parts[1]) * 1000 * 60;
        }
        if (60 > ($milliseconds / 1000 / 60))
        {
            $scratch = ($milliseconds/1000/60);
            $parts = explode(".", $scratch);
            $return[$milliseconds] .= $parts[0] . 'sec' .($parts[0]>1?"s ":" ");
            $milliseconds = ((float)"0." . $parts[1]) * 1000 * 60;
        }
        if ($return[$milliseconds]=='')
            $return[$milliseconds] = 'No Time Passed!';
        
        return $return[$milliseconds] = trim($return[$milliseconds]);
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
    function getNTPConf($mode = 'ntp.conf', $format = 'conf')
    {
        $authors = $links = $pools = $servers = array();
        $result = $GLOBALS['APIDB']->queryF($sql = "SELECT DISTINCT `name`, `nameemail` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . '` WHERE `online` > `offline` AND `uptime` > `downtime`');
        while($row = $GLOBALS['APIDB']->fetchArray($result))
            if (!empty($row['name']) && !empty($row['nameemail']))
                if (!isset($authors[$row['nameemail']]))
                    $authors[$row['nameemail']] = sprintf("## @author          %s <%s>", $row['name'], $row['nameemail']);

                    $result = $GLOBALS['APIDB']->queryF("SELECT DISTINCT `companyname`, `companyemail` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . '` WHERE  `online` > `offline` AND `uptime` > `downtime`');
        while($row = $GLOBALS['APIDB']->fetchArray($result))
            if (!empty($row['companyname']) && !empty($row['companyemail']))
                if (!isset($authors[$row['companyemail']]))
                    $authors[$row['companyemail']] = sprintf("## @author          %s <%s>", $row['companyname'], $row['companyemail']);
        $result = $GLOBALS['APIDB']->queryF("SELECT DISTINCT `name`, `nameurl` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . '` WHERE  `online` > `offline` AND `uptime` > `downtime`');
        while($row = $GLOBALS['APIDB']->fetchArray($result))
            if (!empty($row['name']) && !empty($row['nameurl']))
                if (!isset($links[$row['nameurl']]))
                    $links[$row['nameurl']] = sprintf("## @link            %s %s", $row['name'], $row['nameurl']);
        $result = $GLOBALS['APIDB']->queryF("SELECT DISTINCT `companyname`, `companyurl` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . '` WHERE  `online` > `offline` AND `uptime` > `downtime`');
        while($row = $GLOBALS['APIDB']->fetchArray($result))
            if (!empty($row['companyname']) && !empty($row['companyurl']))
                if (!isset($links[$row['companyurl']]))
                    $links[$row['companyurl']] = sprintf("## @link            %s %s", $row['companyname'], $row['companyurl']);
        $result = $GLOBALS['APIDB']->queryF("SELECT DISTINCT `hostname`, `port` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . '` WHERE `typal` = "pool" AND  `online` > `offline` AND `uptime` > `downtime`');
        while($row = $GLOBALS['APIDB']->fetchArray($result))
            if (!empty($row['hostname']))
                if (!isset($pools[$row['hostname']]) && $row['port'] == '123')
                    $pools[$row['hostname']] = sprintf("pool %s iburst", $row['hostname']);
                elseif (!isset($pools[$row['hostname']]) && $row['port'] != '123')
                    $pools[$row['hostname'].":".$row['port']] = sprintf("pool %s:%s iburst", $row['hostname'], $row['port']);
        $result = $GLOBALS['APIDB']->queryF("SELECT DISTINCT `hostname`, `port` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . '` WHERE `typal` = "server" AND  `online` > `offline` AND `uptime` > `downtime`');
        while($row = $GLOBALS['APIDB']->fetchArray($result))
            if (!empty($row['hostname']))
                if (!isset($servers[$row['hostname']]) && $row['port'] == '123')
                    $servers[$row['hostname']] = sprintf("server %s", $row['hostname']);
                elseif (!isset($pools[$row['hostname']]) && $row['port'] != '123')
                    $servers[$row['hostname'].":".$row['port']] = sprintf("server %s:%s", $row['hostname'], $row['port']);
    
        if (count($authors)==0)
            $authors['##'] = '## ';
        
        if (count($links)==0)
            $links['##'] = '## ';
                
        header("Context-Type: text/text");
        die(str_replace('\n\n## ', '\n## ', str_replace('%ip', $_SERVER['REMOTE_ADDR'], str_replace("%url", API_URL . $_SERVER['REQUEST_URI'], str_replace("YYYY/MM/DD HH:II:SS", date("Y-m-d W.Y, D, H:i:s"), str_replace('%servers', implode("\n", $servers), str_replace('%pools', implode("\n", $pools), str_replace('%links', implode("\n", $links), str_replace('%authors', implode("\n", $authors), file_get_contents(__DIR__ . DS . 'data' . DS . 'ntp.conf.txt'))))))))));
    
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


function getHTMLForm($mode = '', $var = '')
{

    $form = array();
    switch ($mode)
    {
        case "addntp":
            $form[] = "<form name='add-ntp' method=\"POST\" enctype=\"multipart/form-data\" action=\"" . API_URL . '/v1/addntp.api">';
            $form[] = "\t<table class='add-ntp' id='auth-key' style='vertical-align: top !important; min-width: 98%;'>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
            $form[] = "\t\t\t\t<label for='hostname'>Hostname/IP Address:&nbsp;<font style='color: rgb(250,0,0); font-size: 139%; font-weight: bold'>*</font></label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='hostname' id='hostname' maxlen='250' size='28'/>&nbsp;&nbsp;";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
            $form[] = "\t\t\t\t<label for='port'>Port:&nbsp;<font style='color: rgb(250,0,0); font-size: 139%; font-weight: bold'>*</font></label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='port' id='port' maxlen='5'  size='8' value='123' />&nbsp;&nbsp;";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
            $form[] = "\t\t\t\t<label for='name'>Person's Name:&nbsp;<font style='color: rgb(250,0,0); font-size: 139%; font-weight: bold'>*</font></label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='name' id='name' maxlen='128' size='28' /><br/>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
            $form[] = "\t\t\t\t<label for='nameurl'>Person's URL:</label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='nameurl' id='nameurl' maxlen='250'  size='28'/><br/>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
            $form[] = "\t\t\t\t<label for='nameemail'>Person's eMail:&nbsp;<font style='color: rgb(250,0,0); font-size: 139%; font-weight: bold'>*</font></label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='nameemail' id='nameemail' maxlen='196'  size='28'/><br/>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
            $form[] = "\t\t\t\t<label for='companyname'>Company's Name:</label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='companyname' id='companyname' maxlen='128'  size='28'/><br/>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
            $form[] = "\t\t\t\t<label for='companyemail'>Company's eMail:</label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='companyemail' id='companyemail' maxlen='196'  size='28'/><br/>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
            $form[] = "\t\t\t\t<label for='companyrbn'>Company's Register Number:</label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='companyrbn' id='companyrbn' maxlen='128'  size='28'/><br/>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
            $form[] = "\t\t\t\t<label for='companyrbntype'>Company's Register Number Type:</label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='companyrbntype' id='companyrbntype' maxlen='13'  size='8'/><br/>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
            $form[] = "\t\t\t\t<label for='companytype'>Company's Type:</label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='companytype' id='companytype' maxlen='64'  size='28'/><br/>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
            $form[] = "\t\t\t\t<label for='companyurl'>Company's URL:</label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='companyurl' id='companyurl' maxlen='250'  size='28'/><br/>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<label for='format'>Output Format:&nbsp;<font style='color: rgb(250,0,0); font-size: 139%; font-weight: bold'>*</font></label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
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
            $form[] = "\t\t\t\t<input type='hidden' value='addntp' name='mode'>";
            $form[] = "\t\t\t\t<input type='submit' value='Add NTP Source to DB' name='submit' style='padding:11px; font-size:122%;'>";
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
            
        case "editntp":
            $form[] = "<form name='edit-ntp' method=\"POST\" enctype=\"multipart/form-data\" action=\"" . API_URL . '/v1/'.$var['key'].'/editntp.api">';
            $form[] = "\t<table class='edit-ntp' id='auth-key' style='vertical-align: top !important; min-width: 98%;'>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
            $form[] = "\t\t\t\t<label for='hostname'>Hostname/IP Address:&nbsp;<font style='color: rgb(250,0,0); font-size: 139%; font-weight: bold'>*</font></label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='hostname' id='hostname' maxlen='250' size='28' value='".$var['hostname']."'/>&nbsp;&nbsp;";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
            $form[] = "\t\t\t\t<label for='port'>Port:&nbsp;<font style='color: rgb(250,0,0); font-size: 139%; font-weight: bold'>*</font></label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='port' id='port' maxlen='5'  size='8' value='123'  value='".$var['port']."'/>&nbsp;&nbsp;";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
            $form[] = "\t\t\t\t<label for='name'>Person's Name:&nbsp;<font style='color: rgb(250,0,0); font-size: 139%; font-weight: bold'>*</font></label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='name' id='name' maxlen='128' size='28'  value='".$var['name']."'/><br/>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
            $form[] = "\t\t\t\t<label for='nameurl'>Person's URL:</label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='nameurl' id='nameurl' maxlen='250'  size='28' value='".$var['nameurl']."'/><br/>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
            $form[] = "\t\t\t\t<label for='nameemail'>Person's eMail:&nbsp;<font style='color: rgb(250,0,0); font-size: 139%; font-weight: bold'>*</font></label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='nameemail' id='nameemail' maxlen='196'  size='28'  value='".$var['nameemail']."'/><br/>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
            $form[] = "\t\t\t\t<label for='companyname'>Company's Name:</label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='companyname' id='companyname' maxlen='128'  size='28' value='".$var['companyname']."'/><br/>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
            $form[] = "\t\t\t\t<label for='companyemail'>Company's eMail:</label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='companyemail' id='companyemail' maxlen='196' size='28' value='".$var['companyemail']."'/><br/>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
            $form[] = "\t\t\t\t<label for='companyrbn'>Company's Register Number:</label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='companyrbn' id='companyrbn' maxlen='128' size='28' value='".$var['companyrbn']."'/><br/>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
            $form[] = "\t\t\t\t<label for='companyrbntype'>Company's Register Number Type:</label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='companyrbntype' id='companyrbntype' maxlen='13'  size='8' value='".$var['companyrbntype']."'/><br/>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
            $form[] = "\t\t\t\t<label for='companytype'>Company's Type:</label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='companytype' id='companytype' maxlen='64'  size='28' value='".$var['companytype']."'/><br/>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
            $form[] = "\t\t\t\t<label for='companyurl'>Company's URL:</label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<input type='textbox' name='companyurl' id='companyurl' maxlen='250'  size='28' value='".$var['companyurl']."'/><br/>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td>&nbsp;</td>";
            $form[] = "\t\t</tr>";
            $form[] = "\t\t<tr>";
            $form[] = "\t\t\t<td>";
            $form[] = "\t\t\t\t<label for='format'>Output Format:&nbsp;<font style='color: rgb(250,0,0); font-size: 139%; font-weight: bold'>*</font></label>";
            $form[] = "\t\t\t</td>";
            $form[] = "\t\t\t<td style='width: 499px;'>";
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
            $form[] = "\t\t\t\t<input type='hidden' value='editntp' name='mode'>";
            $form[] = "\t\t\t\t<input type='hidden' value='key' name='".$var['key']."'>";
            $form[] = "\t\t\t\t<input type='submit' value='Edit NTP Source in DB' name='submit' style='padding:11px; font-size:122%;'>";
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