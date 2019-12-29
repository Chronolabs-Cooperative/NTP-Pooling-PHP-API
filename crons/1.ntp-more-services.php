<?php
/**
 * NTP.SNAILS.EMAIL - Pinging Cron
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

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'apiconfig.php';

$start = time();
if ($staters = APICache::read(str_replace('.php', '', basename(__FILE__))))
{
    $staters[] = $start;
    sort($staters, SORT_ASC);
    if (count($starters)>50)
        unset($starters[0]);
        sort($staters, SORT_ASC);
        APICache::write(str_replace('.php', '', basename(__FILE__)), $staters, 3600 * 24 * 7 * 4 * 6);
        $keys = array_key(array_reverse($starters));
        $avg = array();
        foreach(array_reverse($starters) as $key => $starting) {
            if (isset($keys[$key - 1])) {
                $avg[] = abs($starting - $starters[$keys[$key - 1]]);
            }
        }
        if (count($avg) > 0 ) {
            foreach($avg as $average)
                $seconds += $average;
                $seconds = $seconds / count($avg);
        } else
            $seconds = 1800;
} else {
    APICache::write(str_replace('.php', '', basename(__FILE__)), array(0=>$start), 3600 * 24 * 7 * 4 * 6);
    $seconds = 1800;
}

$ntpservices = array();
$change = str_replace('-more-services.php', '', basename(__FILE__)) . '.';
$prefixes = array('clock', 'time', 'ntp1', 'ntp2', 'ntp3', 'ntp4', 'ntp5', 'ntp6', 'ntp7', 'ntp8', 'ntp9', 'ntp10', 'ntp11', 'ntp12', 'ntp13', 'ntp14', 'ntp15', 'ntp16', 'ntp17', 'ntp18', 'ntp19', 'ntp20', 'ntp21', 'ntp22', 'ntp23', 'ntp24', 'ntp25', 'ntp26', 'ntp27', 'ntp28', 'ntp29', 'ntp30', 'ntp31', 'ntp32', 'clock', '1.ntp', '2.ntp', '3.ntp', '4.ntp', '5.ntp', '6.ntp', '7.ntp', '8.ntp', '9.ntp', '10.ntp', '11.ntp', '12.ntp', '13.ntp', '14.ntp', '15.ntp', '16.ntp', '17.ntp', '18.ntp', '19.ntp', '20.ntp', '21.ntp', '22.ntp', '23.ntp', '24.ntp', '25.ntp', '26.ntp', '27.ntp', '28.ntp', '29.ntp', '30.ntp', '31.ntp', '32.ntp', '0.ntp');
$question = "SELECT * FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE `hostname` LIKE '$change%' AND `pinging` > 0 AND `uptime` > `downtime` AND `uptime` > 0 ORDER BY `mored` ASC, RAND() LIMIT " . mt_rand(29, 59);
echo "SQL Clausing: $question;\n\n";
$result = $GLOBALS['APIDB']->queryF($question);
while($ntpservice = $GLOBALS['APIDB']->fetchArray($result)) {
    @$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` SET `mored` = UNIX_TIMESTAMP() WHERE `id` = " . $ntpservice['id']);
    $ntpservices[$ntpservice['id']] = $ntpservice;
}
foreach($ntpservices as $ntpid => $ntpservice) {
    $added = $skipped = 0;
    $hname = $ntpservice['hostname'];
    $hostname = str_replace($change, 'ntp.', $hname);
    list($count) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF("SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE `hostname` LIKE '$hostname'"));
    if ($count==0) {
        echo ("\nPinging " . $hostname . ":" . $ntpservice['port'] . ' ' );
        if ($now = (float)getTimeFromNTP($hostname, $ntpservice['port']))
            $ping = getHostnamePing($hostname, $ntpservice['port']);
        else
            $ping = 0;
        if ($now > 0 && $ping == false)
            $ping = mt_rand(10000, 654999) / 1000000;
        echo "- ".$ping . "ms - " . date("Y-m-d D, W, H:i:s", $now) . "\n";
        if ($ping > false && $now > 0) {
            addNTP($hostname, $ntpservice['port'], $ntpservice['name'], $ntpservice['nameemail'], $ntpservice['nameurl'], $ntpservice['companyname'], $ntpservice['companyemail'], $ntpservice['companyrbn'], $ntpservice['companyrbntype'], $ntpservice['companytype'], $ntpservice['companyurl'], 'json');
            $added++;
        } else {
            $skipped++;
            echo ("\nSkipping already Exists " . ($hostname) . ":" . $ntpservice['port'] . "\n" );
        }
        foreach($prefixes as $prefix) {
            $hostname = str_replace($change, $prefix.'.', $hname);
            list($count) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF("SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE `hostname` LIKE '$hostname'"));
            if ($count==0) {
                echo ("\nPinging " . ($hostname) . ":" . $ntpservice['port'] . ' ' );
                if ($now = (float)getTimeFromNTP($hostname, $ntpservice['port']))
                    $ping = getHostnamePing($hostname, $ntpservice['port']);
                else
                    $ping = 0;
                if ($now > 0 && $ping == false)
                    $ping = mt_rand(10000, 654999) / 1000000;
                echo "- ".$ping . "ms - " . date("Y-m-d D, W, H:i:s", $now) . "\n";
                if ($ping > false && $now > 0) {
                    addNTP($hostname, $ntpservice['port'], $ntpservice['name'], $ntpservice['nameemail'], $ntpservice['nameurl'], $ntpservice['companyname'], $ntpservice['companyemail'], $ntpservice['companyrbn'], $ntpservice['companyrbntype'], $ntpservice['companytype'], $ntpservice['companyurl'], 'json');
                    $added++;
                } else
                    $skipped++;
            } else {
                $skipped++;
                echo ("\nSkipping already Exists " . ($hostname) . ":" . $ntpservice['port'] . "\n" );
            }
            if ($added >= 3 && $skipped <= 6) {
                continue;
                continue;
            }
        }
    } else {
        echo ("\nSkipping already Exists " . ($hostname) . ":" . $ntpservice['port'] . "\n" );
    }
    @$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` SET `mored` = UNIX_TIMESTAMP() WHERE `id` = " . $ntpid);
}
