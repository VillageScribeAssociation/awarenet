<?php

	require_once($kapenta->installPath . 'modules/newsletter/models/edition.mod.php');
	require_once($kapenta->installPath . 'modules/newsletter/models/category.mod.php');
	require_once($kapenta->installPath . 'modules/newsletter/models/notice.mod.php');
	require_once($kapenta->installPath . 'modules/newsletter/models/subscription.mod.php');
	require_once($kapenta->installPath . 'modules/newsletter/inc/send.inc.php');

//--------------------------------------------------------------------------------------------------
//*	publish an edition of the newsletter
//--------------------------------------------------------------------------------------------------
//postart: UID - UID of an unpublished Newsletter_Edition obejct [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('Edition UID not given'); }

	$model = new Newsletter_Edition($_POST['UID']);
	if (false == $model->loaded) { $page->do404('unknown edition'); }

	//----------------------------------------------------------------------------------------------
	//	compose the email
	//----------------------------------------------------------------------------------------------

	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]');
	echo "<h2>Sending out: " . $model->subject . "</h2>";

	$emailHtml = $theme->expandBlocks('[[:newsletter::mailtemplate::UID=' . $model->UID . ':]]');
	if ('' == $emailHtml) { $page->do404('Error composing email'); }

	//----------------------------------------------------------------------------------------------
	//	send the mail
	//----------------------------------------------------------------------------------------------
	$conditions = array("status='subscribed'");
	$range = $db->loadRange('newsletter_subscription', '*', $conditions);

	foreach($range as $item) {
		//TODO: send the email
		echo "<div class='chatmessageblack'>Subscriber: " . $item['email'] . "</div>";

		newsletter_send($emailHtml, $item['email']);

		$model->sentto .= $item['email'] . "<br/>\n";
	}


	//----------------------------------------------------------------------------------------------
	//	update the edition
	//----------------------------------------------------------------------------------------------

	$model->publishdate = $kapenta->datetime();
	$model->status = 'published';
	$report = $model->save();	

	if ('' == $report) {
		$session->msg("Edition published: " . $model->subject);
	} else {
		$session->msg("Error while publishiung: " . $model->subject . "<br/>" . $report);
	}

	//----------------------------------------------------------------------------------------------
	//	send admin mail
	//----------------------------------------------------------------------------------------------

	//TODO:

	//----------------------------------------------------------------------------------------------
	//	redirect back to home page
	//----------------------------------------------------------------------------------------------
	
	echo "<h2>Done.</h2>";
	echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]');

	//$page->do302('newsletter');


?>
