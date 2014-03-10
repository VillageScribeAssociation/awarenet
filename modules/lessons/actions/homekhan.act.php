<?php

	require_once($kapenta->installPath . 'modules/lessons/inc/khan.inc.php');

//--------------------------------------------------------------------------------------------------
//*	This action starts the watch video do exercises functionality for KA Lite
//--------------------------------------------------------------------------------------------------
	global $kapenta;

	if ('admin' !== $kapenta->user->role 
	and 'teacher' !== $kapenta->user->role
	and 'student' !== $kapenta->user->role) { $kapenta->page->do403(); }

	$sessionid = '';
	$csrftoken = '';
	$kalite = $kapenta->registry->get('kalite.installation');
	
	//----------------------------------------------------------------------------------------------
	//	check if user is already logged in into KA Lite, otherwise automatically create user (1st time) and log in
	//----------------------------------------------------------------------------------------------
	$url = 'http://localhost/api/status';
	$reply = $kapenta->utils->curlGet($url, '', false);
	if (0 < strpos($reply, '"is_logged_in": true')) {
//	if (true == $kapenta->session->has('c_sessionid') and '' !== $kapenta->session->get('c_sessionid')) {
		//signed in already, continue below
		//echo "We are logged in with KhanLite already!<br/>\n";
	} else {
//		echo "We are not logged in with KhanLite!<br/>\n";
		logoutKhanLite();
		createAndLoginKhanLite();
	} 

	//----------------------------------------------------------------------------------------------
	//	call GET /watch and exercise from KA Lite Server
	//----------------------------------------------------------------------------------------------
	$sessionid = $kapenta->session->get('c_sessionid');
	$raw = $kapenta->utils->curlGet($kalite, '', false, 'sessionid='.$sessionid);

	//----------------------------------------------------------------------------------------------
	//	remove internal KA Lite links so that we can control what functionality of KA Lite is called from Awarenet
	//----------------------------------------------------------------------------------------------

    $replaced = trimKAPage($raw);
	$replaced = removeLinksFromKhanLitePage($replaced);

	//----------------------------------------------------------------------------------------------
	//	Render KA Lite sub page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/lessons/actions/khansub.page.php');
	$kapenta->page->blockArgs['kalisting'] = $replaced;
	$kapenta->page->render();	
?>
