<?php

	require_once($kapenta->installPath . 'modules/lessons/inc/khan.inc.php');

	global $kapenta;

	if ('admin' !== $kapenta->user->role 
	and 'teacher' !== $kapenta->user->role
	and 'student' !== $kapenta->user->role) { $kapenta->page->do403(); }

	$sessionid = '';
	$csrftoken = '';
	$kalite = $kapenta->registry->get('kalite.installation');
	
	if (true == $kapenta->session->has('c_sessionid') and '' !== $kapenta->session->get('c_sessionid')) {
		//signed in already, continue below
//		echo "We are logged in with KhanLite already!<br/>\n";
	} else {
//		echo "We are not logged in with KhanLite!<br/>\n";
		createAndLoginKhanLite();
	} 

	$sessionid = $kapenta->session->get('c_sessionid');
	$raw = $kapenta->utils->curlGet($kalite, '', false, 'sessionid='.$sessionid);

	//5) remove links to other locations within khanlite from received html page
	$replaced = removeLinksFromKhanLitePage($raw);

	//6) display html page within the awarenet context
	$kapenta->page->load('modules/lessons/actions/khan.page.php');
	$kapenta->page->blockArgs['kalisting'] = $replaced;
	$kapenta->page->render();	
?>
