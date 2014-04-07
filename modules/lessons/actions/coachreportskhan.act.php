<?php

	require_once($kapenta->installPath . 'modules/lessons/inc/khan.inc.php');

//--------------------------------------------------------------------------------------------------
//*	This action starts the coach reports functionality for KA Lite
//--------------------------------------------------------------------------------------------------

	global $kapenta;

	if ('admin' !== $kapenta->user->role 
	and 'teacher' !== $kapenta->user->role) { $kapenta->page->do403(); }

	$kalite = $kapenta->registry->get('kalite.installation');
	
	$cookies = "";
	if (true == $kapenta->session->has('kalite_sessionid')) {
		$sessionid = $kapenta->session->get('kalite_sessionid');
		$cookies = 'sessionid='.$sessionid.';';
	}
	if (true == $kapenta->session->has('kalite_csrftoken')) {
		$csrftoken = 	$kapenta->session->get('kalite_csrftoken');
		$cookies = $cookies . 'csrftoken='.$csrftoken;
	}
	
	//----------------------------------------------------------------------------------------------
	//	check if user is already logged in into KA Lite, otherwise automatically create user (1st time) and log in
	//----------------------------------------------------------------------------------------------
    $time = round(microtime(1) * 1000);	
    $url = $kalite.'/api/status?_=' . $time;
	$reply = $kapenta->utils->curlGet($url, "", false, $cookies);
//	echo $reply;
	if (0 < strpos($reply, '"is_logged_in": true')) {
		//signed in already, continue below
//		echo "We are already logged in with KhanLite!<br/>\n";
	} else {
//		echo "We are not logged in with KhanLite!<br/>\n";
		logoutKhanLite();
		$retarg = createAndLoginKhanLite();
		$kapenta->session->set('kalite_sessionid', $retarg['sessionid']);
		$kapenta->session->set('kalite_csrftoken', $retarg['csrftoken']);
	}
	//----------------------------------------------------------------------------------------------
	//	call GET /coachreports from KA Lite Server
	//----------------------------------------------------------------------------------------------
	if (true == $kapenta->session->has('kalite_sessionid')) {
		$sessionid = $kapenta->session->get('kalite_sessionid');
		$cookies = 'sessionid='.$sessionid.';';
	}
	if (true == $kapenta->session->has('kalite_csrftoken')) {
		$csrftoken = 	$kapenta->session->get('kalite_csrftoken');
		$cookies = $cookies . 'csrftoken='.$csrftoken;
	}
	$raw = $kapenta->utils->curlGet($kalite."/coachreports/", '', false, $cookies);

	//----------------------------------------------------------------------------------------------
	//	remove internal KA Lite links so that we can control what functionality of KA Lite is called from Awarenet
	//----------------------------------------------------------------------------------------------
 	$replaced = trimKAPage($raw);
	$replaced = removeLinksFromKhanLitePage($replaced);
	$replaced = replaceLinksFromKhanLitePage($replaced);
	$replaced = changeLocalLinksFromKhanLitePage($replaced);

	//----------------------------------------------------------------------------------------------
	//	Render KA Lite sub page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/lessons/actions/khansub.page.php');
	$kapenta->page->blockArgs['kalisting'] = $replaced;
	$kapenta->page->render();	
?>
