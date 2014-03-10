<?php

	require_once($kapenta->installPath . 'modules/newsletter/models/edition.mod.php');
	require_once($kapenta->installPath . 'modules/newsletter/inc/send.inc.php');

//--------------------------------------------------------------------------------------------------
//*	send a newsletter to a single address
//--------------------------------------------------------------------------------------------------
//postarg: address - address to send newsletter to [string]
//postarg: UID - UID of a Newsletter_Edition object [string]

	//----------------------------------------------------------------------------------------------
	//	check postargs and user role
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('address', $_POST)) { $kapenta->page->do404('address not specified'); }	
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('edition not specified'); }

	$model = new Newsletter_Edition($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Unknown edition'); }

	$address = $_POST['address'];

	//----------------------------------------------------------------------------------------------
	//	send the email
	//----------------------------------------------------------------------------------------------
	$html = $theme->expandBlocks("[[:newsletter::mailtemplate::UID=" . $model->UID . ":]]");
	$check = newsletter_send($html, $address);

	if (true == $check) {
		$session->msg("Email sent to: " . $address, 'ok');
	} else {
		$session->msg("Email not sent to: " . $address, 'bad');
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to edition
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do302('newsletter/edition/' . $model->alias);

?>
