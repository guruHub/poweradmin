<?php
define('USING_API', true);
error_reporting(E_ALL);

require_once("inc/toolkit.inc.php");

// POWERADMIN API ABSTRACTION
// ACTIONS :
//	create_zone
//		domain
//		content
//		owner_id
	
extract($_GET);
$_SESSION['userid'] = 2;

switch($action){
	case "create_zone" : 
		gh_create_zone($domain, $content, $owner_id);
	break;			
	case "set_google_apps_records" : 
		gh_set_google_apps_records($domain);
	break;
	case "create_subdomain" : 
		gh_create_subdomain($domain, $subdomain, $content);
	break;
}


function gh_create_zone($domain, $content, $owner_id){

	$domain =  strtolower($domain);
	$dom_type = 'MASTER';
	$zone_template = 'none';

	add_domain($domain, $owner_id, $dom_type, '', $zone_template);

	$zoneid = get_zone_id_from_name($domain);
	$type = 'A';
	$content = $content;
	$ttl = 86400;
	$prio = 0;
	$name = $domain;
	add_record($zoneid, $name, $type, $content, $ttl, $prio);
	$name = "www.{$domain}";
	add_record($zoneid, $name, $type, $content, $ttl, $prio);
	return $zoneid;
}

function gh_set_google_apps_records($domain){

	$domain =  strtolower($domain);
	$zoneid = get_zone_id_from_name($domain);
	$type = 'MX';
	$ttl = 86400;
	$prio = 0;
	$name = $domain;

	$contents[] = "ASPMX.L.GOOGLE.COM.";
	$contents[] = "ALT1.ASPMX.L.GOOGLE.COM.";
	$contents[] = "ALT2.ASPMX.L.GOOGLE.COM.";
	$contents[] = "ASPMX2.GOOGLEMAIL.COM.";
	$contents[] = "ASPMX3.GOOGLEMAIL.COM.";
	
	foreach($contents as $content){
		add_record($zoneid, $name, $type, $content, $ttl, $prio += 10);
	}

	$names = array("mail", "calendar", "docs", "start", "sites");
	$content = "ghs.google.com";
	$type = 'CNAME';

	foreach($names as $name){
		add_record($zoneid, $name, $type, $content, $ttl, 0);
	}
}

function gh_create_subdomain($domain, $subdomain, $content=null){
	$zoneid = get_zone_id_from_name($domain);
	$type = 'A';
	$content = $content;
	$ttl = 86400;
	$prio = 0;
	$name = "{$subdomain}";
	$content = ($content) ? $content : get_domain_ip($domain);
	add_record($zoneid, $name, $type, $content, $ttl, $prio);
	return $zoneid;
}

function gh_set_cpanel_mx($domain){
	$zoneid = get_zone_id_from_name($domain);
	$type = 'MX';
	$content = "mx.registrar.com.uy"
	$ttl = 86400;
	$prio = 0;
	add_record($zoneid, "", $type, $content, $ttl, $prio);
	return $zoneid;
}
