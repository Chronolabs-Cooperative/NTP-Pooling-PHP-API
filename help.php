<?php
/**
 * DNS Zone Propogation REST Services API
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
 * @since           1.0.3
 * @author          Dr. Simon Antony Roberts <simon@snails.email>
 * @version         1.0.3
 * @description		A REST API for the creation and management of emails/forwarders and domain name parks for email
 * @link            http://internetfounder.wordpress.com
 * @link            https://github.com/Chronolabs-Cooperative/Emails-API-PHP
 * @link            https://sourceforge.net/p/chronolabs-cooperative
 * @link            https://facebook.com/ChronolabsCoop
 * @link            https://twitter.com/ChronolabsCoop
 * 
 */

    ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta property="og:title" content="<?php echo API_VERSION; ?>"/>
<meta property="og:type" content="api<?php echo API_TYPE; ?>"/>
<meta property="og:image" content="<?php echo API_URL; ?>/assets/images/logo_500x500.png"/>
<meta property="og:url" content="<?php echo (isset($_SERVER["HTTPS"])?"https://":"http://").$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]; ?>" />
<meta property="og:site_name" content="<?php echo API_VERSION; ?> - <?php echo API_LICENSE_COMPANY; ?>"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="rating" content="general" />
<meta http-equiv="author" content="wishcraft@users.sourceforge.net" />
<meta http-equiv="copyright" content="<?php echo API_LICENSE_COMPANY; ?> &copy; <?php echo date("Y"); ?>" />
<meta http-equiv="generator" content="Chronolabs Cooperative (<?php echo $place['iso3']; ?>)" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo API_VERSION; ?> || <?php echo API_LICENSE_COMPANY; ?></title>
<!-- AddThis Smart Layers BEGIN -->
<!-- Go to http://www.addthis.com/get/smart-layers to customize -->
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-50f9a1c208996c1d"></script>
<script type="text/javascript">
  addthis.layers({
	'theme' : 'transparent',
	'share' : {
	  'position' : 'right',
	  'numPreferredServices' : 6
	}, 
	'follow' : {
	  'services' : [
		{'service': 'facebook', 'id': 'Chronolabs'},
		{'service': 'twitter', 'id': 'JohnRingwould'},
		{'service': 'twitter', 'id': 'ChronolabsCoop'},
		{'service': 'twitter', 'id': 'Cipherhouse'},
		{'service': 'twitter', 'id': 'OpenRend'},
	  ]
	},  
	'whatsnext' : {},  
	'recommended' : {
	  'title': 'Recommended for you:'
	} 
  });
</script>
<!-- AddThis Smart Layers END -->
<link rel="stylesheet" href="<?php echo API_URL; ?>/assets/css/style.css" type="text/css" />
<!-- Custom Fonts -->
<link href="<?php echo API_URL; ?>/assets/media/Labtop/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Labtop Bold/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Labtop Bold Italic/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Labtop Italic/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Labtop Superwide Boldish/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Labtop Thin/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Labtop Unicase/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/LHF Matthews Thin/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Life BT Bold/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Life BT Bold Italic/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Prestige Elite/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Prestige Elite Bold/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Prestige Elite Normal/style.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo API_URL; ?>/assets/css/gradients.php" type="text/css" />
<link rel="stylesheet" href="<?php echo API_URL; ?>/assets/css/shadowing.php" type="text/css" />

</head>
<body>
<div class="main">
	<img style="float: right; margin: 11px; width: auto; height: auto; clear: none;" src="<?php echo API_URL; ?>/assets/images/logo_350x350.png" />
    <h1><?php echo API_URL; ?> -- <?php echo API_LICENSE_COMPANY; ?></h1>
    <p>As an API, this allows you to anonymously without authentication add either IPv4, IPv6 or Network Netbios Hostnames for NTP Time based services on the internet and the wider worlds of networking.</p>
    <p>You can get the NTP Protocol this is a time exchange based protocol which computers and the internet use to lookup and calibrate your own system clocks; so to use this you would from the following URL on your Windows or Macintosh Control Panel + System Settings in the time options; it also work in your internet router at home or in the office; put the following host pathname:<p>
    <p style="text-align: center; font-size: 245%"><em><strong><?php echo parse_url(API_URL, PHP_URL_HOST); ?></strong></em></p>
    <p style="font-size: 145%">For any on going details of this timing-bell please referee to our online documentation here: <a href="https://sourceforge.net/p/chronolabs-cooperative/wiki/NTP%20Servers%20Pooling/" target="_blank">https://sourceforge.net/p/chronolabs-cooperative/wiki/NTP Servers Pooling/</a>...</p>
    <h2>Code API Documentation</h2>
    <p>You can find the phpDocumentor code API documentation at the following path :: <a href="<?php echo API_URL . '/'; ?>docs/" target="_blank"><?php echo API_URL . '/'; ?>docs/</a>. These should outline the source code core functions and classes for the API to function!</p>
    <h2>ADDNTP Document Output</h2>
    <p>This is done with the <em>add.api</em> extension at the end of the url, you replace the example address with either a domain!</p>
    <blockquote>
        <?php echo getHTMLForm('addntp'); ?>
    </blockquote>
    <h3>This the HTML Code surrounding the api call</h3>
    <pre style="max-height: 300px; overflow: scroll;">
    <?php echo htmlspecialchars(getHTMLForm('addntp')); ?>
    </pre>
    <h2>NTP.CONF Document Output</h2>
    <p>This is done with the <em>ntp.conf</em> extension at the end of the url, you replace the example address with either a domain!</p>
    <blockquote>
        <font class="help-title-text">This provides a complete /etc/ntp.conf for debian/ubuntu at least that you can replace your existing one with</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/ntp.conf" target="_blank"><?php echo API_URL . '/'; ?>v1/ntp.conf</a></font><br />
        <font class="help-title-text">Pooled results of 8 pools and return pool number <?php echo $pool = mt_rand(1, 8); ?> of /etc/ntp.conf for debian/ubuntu at least that you can replace your existing one with</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/<?php echo $pool ; ?>/8/ntp.conf" target="_blank"><?php echo API_URL . '/'; ?>v1/<?php echo $pool ; ?>/8/ntp.conf</a></font><br /><br />
    </blockquote>
    <h3>This the SH Cron batch script you should run on adverage every four hours and fifteen minutes!</h3>
    <pre style="max-height: 300px; overflow: scroll;">
##
## file: /var/replace-ntp.conf.sh
##
## CronJob:-
## */15 */4 * * * sh /var/replace-ntp.conf.sh
##
##
cd /tmp
rm -vf ntp.conf
wget <?php echo API_URL . '/'; ?>v1/ntp.conf
if [ -s "/tmp/ntp.conf" ]
then
   rm -vf /etc/ntp.conf 
   mv /tmp/ntp.conf /etc
   chmod -fv 0644 /etc/ntp.conf
   service ntp reload
else
   echo " ntp.conf was returned empty or not existing! "
fi
####################################################<?php echo date("[ Y-m-d W.y, D, H:i:s ]");?>######
    </pre>
    <h2>RSS Document Output</h2>
    <p>This is done with the <em>command.rss</em> extension at the end of the url.</p>
    <blockquote>
        <font class="help-title-text">This provides a RSS Feed of the top ntp.snail.email providers, pools and servers</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/top.rss?20" target="_blank"><?php echo API_URL . '/'; ?>v1/top.rss?20</a></font><br /><br />
        <font class="help-title-text">This provides a RSS Feed of the worst ntp.snail.email providers, pools and servers</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/worse.rss?20" target="_blank"><?php echo API_URL . '/'; ?>v1/worse.rss?20</a></font><br /><br />
        <font class="help-title-text">This provides a RSS Feed of the new ntp.snail.email providers, pools and servers</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/new.rss?20" target="_blank"><?php echo API_URL . '/'; ?>v1/new.rss?20</a></font><br /><br />
    </blockquote>
    <h2>PHP Document Output</h2>
    <p>This is done with the <em>command.php</em> extension at the end of the url.</p>
    <blockquote>
        <font class="help-title-text">This provides a list and keys of defined NTP Source tested to currently be online from host on the service</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/online.php" target="_blank"><?php echo API_URL . '/'; ?>v1/online.php</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined NTP Source tested to currently be offline from host on the service</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/offline.php" target="_blank"><?php echo API_URL . '/'; ?>v1/offline.php</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined in the offline.php/online.php api call as well as the ping time (least to greatest)</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/pings.php" target="_blank"><?php echo API_URL . '/'; ?>v1/pings.php</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined in the offline.php/online.php api call as well as the up-time (least to greatest)</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/uptime.php" target="_blank"><?php echo API_URL . '/'; ?>v1/uptime.php</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined in the offline.php/online.php api call as well as the down-time (least to greatest)</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/downtime.php" target="_blank"><?php echo API_URL . '/'; ?>v1/downtime.php</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined in the offline.php/online.php api call as well as the next time the key is due to be pinged by timeout (least to greatest)</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/nextping.php" target="_blank"><?php echo API_URL . '/'; ?>v1/nextping.php</a></font><br /><br />
        <font class="help-title-text">This provides a list of the companies which are currently online from closest ping to least!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/companies.php" target="_blank"><?php echo API_URL . '/'; ?>v1/companies.php</a></font><br /><br />
        <font class="help-title-text">This provides a list of the companies which are currently offline currently!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/offcompanies.php" target="_blank"><?php echo API_URL . '/'; ?>v1/offcompanies.php</a></font><br /><br />
    </blockquote>
    <!--<h2>ASP Document Output</h2>
    <p>This is done with the <em>command.asp</em> extension at the end of the url.</p>
    <blockquote>
        <font class="help-title-text">This provides a list and keys of defined NTP Source tested to currently be online from host on the service</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/online.asp" target="_blank"><?php echo API_URL . '/'; ?>v1/online.asp</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined NTP Source tested to currently be offline from host on the service</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/offline.asp" target="_blank"><?php echo API_URL . '/'; ?>v1/offline.asp</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined in the offline.asp/online.asp api call as well as the ping time (least to greatest)</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/pings.asp" target="_blank"><?php echo API_URL . '/'; ?>v1/pings.asp</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined in the offline.asp/online.asp api call as well as the up-time (least to greatest)</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/uptime.asp" target="_blank"><?php echo API_URL . '/'; ?>v1/uptime.asp</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined in the offline.asp/online.asp api call as well as the down-time (least to greatest)</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/downtime.asp" target="_blank"><?php echo API_URL . '/'; ?>v1/downtime.asp</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined in the offline.asp/online.asp api call as well as the next time the key is due to be pinged by timeout (least to greatest)</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/nextping.asp" target="_blank"><?php echo API_URL . '/'; ?>v1/nextping.asp</a></font><br /><br />
    </blockquote>-->
    <h2>Serialisation Document Output</h2>
    <p>This is done with the <em>command.serial</em> extension at the end of the url.</p>
    <blockquote>
        <font class="help-title-text">This provides a list and keys of defined NTP Source tested to currently be online from host on the service</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/online.serial" target="_blank"><?php echo API_URL . '/'; ?>v1/online.serial</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined NTP Source tested to currently be offline from host on the service</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/offline.serial" target="_blank"><?php echo API_URL . '/'; ?>v1/offline.serial</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined in the offline.serial/online.serial api call as well as the ping time (least to greatest)</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/pings.serial" target="_blank"><?php echo API_URL . '/'; ?>v1/pings.serial</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined in the offline.serial/online.serial api call as well as the up-time (least to greatest)</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/uptime.serial" target="_blank"><?php echo API_URL . '/'; ?>v1/uptime.serial</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined in the offline.serial/online.serial api call as well as the down-time (least to greatest)</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/downtime.serial" target="_blank"><?php echo API_URL . '/'; ?>v1/downtime.serial</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined in the offline.serial/online.serial api call as well as the next time the key is due to be pinged by timeout (least to greatest)</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/nextping.serial" target="_blank"><?php echo API_URL . '/'; ?>v1/nextping.serial</a></font><br /><br />
        <font class="help-title-text">This provides a list of the companies which are currently online from closest ping to least!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/companies.serial" target="_blank"><?php echo API_URL . '/'; ?>v1/companies.serial</a></font><br /><br />
        <font class="help-title-text">This provides a list of the companies which are currently offline currently!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/offcompanies.serial" target="_blank"><?php echo API_URL . '/'; ?>v1/offcompanies.xml</a></font><br /><br />
    </blockquote>
    <h2>JSON Document Output</h2>
    <p>This is done with the <em>command.json</em> extension at the end of the url, you replace the address with either a domain, an IPv4 or IPv6 address the following example is of calls to the api</p>
    <blockquote>
        <font class="help-title-text">This provides a list and keys of defined NTP Source tested to currently be online from host on the service</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/online.json" target="_blank"><?php echo API_URL . '/'; ?>v1/online.json</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined NTP Source tested to currently be offline from host on the service</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/offline.json" target="_blank"><?php echo API_URL . '/'; ?>v1/offline.json</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined in the offline.json/online.json api call as well as the ping time (least to greatest)</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/pings.json" target="_blank"><?php echo API_URL . '/'; ?>v1/pings.json</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined in the offline.json/online.json api call as well as the up-time (least to greatest)</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/uptime.json" target="_blank"><?php echo API_URL . '/'; ?>v1/uptime.json</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined in the offline.json/online.json api call as well as the down-time (least to greatest)</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/downtime.json" target="_blank"><?php echo API_URL . '/'; ?>v1/downtime.json</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined in the offline.json/online.json api call as well as the next time the key is due to be pinged by timeout (least to greatest)</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/nextping.json" target="_blank"><?php echo API_URL . '/'; ?>v1/nextping.json</a></font><br /><br />
        <font class="help-title-text">This provides a list of the companies which are currently online from closest ping to least!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/companies.json" target="_blank"><?php echo API_URL . '/'; ?>v1/companies.json</a></font><br /><br />
        <font class="help-title-text">This provides a list of the companies which are currently offline currently!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/offcompanies.json" target="_blank"><?php echo API_URL . '/'; ?>v1/offcompanies.xml</a></font><br /><br />
	</blockquote>
    <h2>XML Document Output</h2>
    <p>This is done with the <em>command.xml</em> extension at the end of the url, you replace the address with either a domain, an IPv4 or IPv6 address the following example is of calls to the api</p>
    <blockquote>
        <font class="help-title-text">This provides a list and keys of defined NTP Source tested to currently be online from host on the service</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/online.xml" target="_blank"><?php echo API_URL . '/'; ?>v1/online.xml</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined NTP Source tested to currently be offline from host on the service</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/offline.xml" target="_blank"><?php echo API_URL . '/'; ?>v1/offline.xml</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined in the offline.xml/online.xml api call as well as the ping time (least to greatest)</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/pings.xml" target="_blank"><?php echo API_URL . '/'; ?>v1/pings.xml</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined in the offline.xml/online.xml api call as well as the up-time (least to greatest)</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/uptime.xml" target="_blank"><?php echo API_URL . '/'; ?>v1/uptime.xml</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined in the offline.xml/online.xml api call as well as the down-time (least to greatest)</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/downtime.xml" target="_blank"><?php echo API_URL . '/'; ?>v1/downtime.xml</a></font><br /><br />
        <font class="help-title-text">This provides a list and keys of defined in the offline.xml/online.xml api call as well as the next time the key is due to be pinged by timeout (least to greatest)</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/nextping.xml" target="_blank"><?php echo API_URL . '/'; ?>v1/nextping.xml</a></font><br /><br />
        <font class="help-title-text">This provides a list of the companies which are currently online from closest ping to least!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/companies.xml" target="_blank"><?php echo API_URL . '/'; ?>v1/companies.xml</a></font><br /><br />
        <font class="help-title-text">This provides a list of the companies which are currently offline currently!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL . '/'; ?>v1/offcompanies.xml" target="_blank"><?php echo API_URL . '/'; ?>v1/offcompanies.xml</a></font><br /><br />    
    </blockquote>
    <h2>The Author</h2>
    <p>This was developed by Dr. Simon Antony Roberts in 2019 and is part of the Chronolabs System and api's.<br/><br/>This is open source which you can download from <a href="https://sourceforge.net/p/chronolabs-cooperative/ntp-pooling-api-php/ci/master/tree/">https://sourceforge.net/p/chronolabs-cooperative/ntp-pooling-api-php/ci/master/tree/</a> contact the scribe  <a href="mailto:wishcraft@users.sourceforge.net">wishcraft@users.sourceforge.net</a></p></body>
</div>
</html>
<?php 
