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
$question = "SELECT DISTINCT `hostname` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE 1 = 1 ORDER BY RAND() LIMIT 2750";
echo "SQL Clausing: $question;\n\n";
$result = $GLOBALS['APIDB']->queryF($question);
while($ntpservice = $GLOBALS['APIDB']->fetchArray($result)) {
    list($count) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF("SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE `hostname` = '" . $ntpservice['hostname'] . "'"));
    if ($count>1) {
        $resulter = $GLOBALS['APIDB']->queryF("SELECT `id` FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE `hostname` = '" . $ntpservice['hostname'] . "' ORDER BY `id`");
        $i=0;
        $ids = array();
        while($row = $GLOBALS['APIDB']->fetchArray($resulter)) {
            $i++;
            if ($i>1)
                $ids[] = $row['id'];
        }
        if ($GLOBALS['APIDB']->queryF("DELETE FROM `" . $GLOBALS['APIDB']->prefix('ntpservices') . "` WHERE `id` IN ('" . implode("', '", $ids) . "')"))
            echo "Deleted for " . $ntpservice['hostname'] . " identities (" . implode(', ', $ids) . ")\n\n";
    }
}
