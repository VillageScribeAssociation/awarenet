<?php

//--------------------------------------------------------------------------------------------------
//*	utility functions for sending email
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	send an html email to the given address
//--------------------------------------------------------------------------------------------------
//arg: html - pre-rendered HTML email
//arg: address - email address to send to
//returns: true on success, false on failure [bool]

function newsletter_send($html, $address) {
	global $kapenta;
	
	$fileName = 'data/outbox/' . $kapenta->date() . ' ' . $address . ".html";
	
	$kapenta->fileMakeSubdirs($html);
	$kapenta->fs->put($fileName, $html);

	return false;
}

?>
