<?
	require_once($kapenta->installPath . 'modules/lessons/inc/khan.inc.php');

//-------------------------------------------------------------------------------------------------
//|	fired when the user logs out.php
//-------------------------------------------------------------------------------------------------
//arg: method - http POST or GET [string]
//arg: uri - uri of http call [string]
//arg: query - parameters in http call [string]
//arg: remoteAddr - address of caller [string]
//arg: remotePort - port of caller [string]
//arg: remotePort - args of POST [string]

function lessons__cb_users_logout($args) {
	global $kapenta;

	$reply = $kapenta->utils->curlGet($kapenta->registry->get('kalite.installation').'/securesync/logout/', '', false, '');
	$kapenta->session->set('c_sessionid', '');
	$kapenta->session->set('c_csrftoken', '');	

	echo $reply;
	//----------------------------------------------------------------------------------------------
	//	ok, done
	//----------------------------------------------------------------------------------------------	
	return true;
}

//-------------------------------------------------------------------------------------------------
?>
