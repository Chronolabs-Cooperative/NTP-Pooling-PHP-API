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

define("_FORM_ENTER", "You must enter the field '%s' this is required for the submission of this form!");

define('API_ZONE_URL', 'http://zones.vps-a.snails.email');
define('API_ZONE_USERNAME', '');
define('API_ZONE_PASSWORD', '');
define('API_ZONE_DOMAIN', 'snails.email');
define('API_ZONE_SUBDOMAIN', '%s.%s.ntp.snails.email');
define('API_ZONE_AUTHKEY', '%apiurl/v1/authkey.api');
define('API_ZONE_DOMAINKEYS', '%apiurl/v1/%authkey/domains/json.api');
define('API_ZONE_DNSRECORDS', '%apiurl/v1/%authkey/%domainkey/zones/json.api');
define('API_ZONE_EDITRECORD', '%apiurl/v1/%authkey/%recordkey/edit/zone/json.api');
define('API_ZONE_DELETERECORD', '%apiurl/v1/%authkey/%recordkey/delete/zone/json.api');
define('API_ZONE_ADDRECORD', '%apiurl/v1/%authkey/zones.api');
define('API_ZONE_CNAMETYPE', 'CNAME');
