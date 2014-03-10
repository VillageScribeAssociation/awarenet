<?php

	require_once($kapenta->installPath . 'modules/newsletter/models/subscription.act.php');

//--------------------------------------------------------------------------------------------------
//*	respond to publis signup via ajax (../views/signupform.block.php)
//--------------------------------------------------------------------------------------------------
//postarg: email - email address of new subscriber [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------

	if (false == array_key_exists('email', $_POST)) {
		echo "Error: email address not given.";
		die();
	}

	$email = trim($_POST['email']);

	$atPos = strpos($email, '@');
	$dotPos = strpos($email, '.', );

	$notAllowed = array(' ', "\t", "\r", "\n", "\\", ';', ':', "`", '"', "'");

	foreach($notAllowed as $check) {
		if (false !== strpos($email, $check)) {
			echo "Notice: invlaid email address, not signed up.";
			die();
		}
	}

	if (strlen($email) > 100) {
		echo "Notice: suspiciously long email address, not signed up.";
		die();
	}

	//----------------------------------------------------------------------------------------------
	//	check database
	//----------------------------------------------------------------------------------------------

	$conditions = array("email='" . $kapenta->db->addMarkup($email) . "'");
	$range = $kapenta->db->loadRange('newsletter_subscription', '*', $conditions);
	$extant = false;

	foreach($range as $item) {
		$extant = true;
		if ('subscribed' == $item['status']) {

			//--------------------------------------------------------------------------------------
			//	user already has an active subscription
			//--------------------------------------------------------------------------------------
			echo "Notice: you are already subscribed to this newsletter.";
			die();

		} else {

			//--------------------------------------------------------------------------------------
			//	user has an inactive subscription
			//--------------------------------------------------------------------------------------

			$model = new Newsletter_Subscription($item['UID']);
			$model->status = 'unconfirmed';
			$model->save();

			echo "You have an unconfirmed subscription...<br/>";

		}
	}

	//----------------------------------------------------------------------------------------------
	//	create new subscription if none exist
	//----------------------------------------------------------------------------------------------

	if (false == $extant) {
		$model = new Newsletter_Subscription();
		$model->email = $email;
		$model->status = 'unconfirmed';
		$model->save();
	}

	//----------------------------------------------------------------------------------------------
	//	send the cofnirmation email
	//----------------------------------------------------------------------------------------------

	//

	echo "Confirmation email sent.";
	die();


?>
