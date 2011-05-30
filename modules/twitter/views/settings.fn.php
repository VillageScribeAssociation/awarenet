<?

//--------------------------------------------------------------------------------------------------
//|	twitter settings form
//--------------------------------------------------------------------------------------------------

function twitter_settings($args) {
	global $theme, $user, $registry;
	$html = '';			//%	return value [string:html]

	//----------------------------------------------------------------------------------------------
	//	check that user is an admin
	//----------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/twitter/views/settings.block.php');

	$labels = array(
		'twitter.consumerkey' => $registry->get('twitter.consumerkey'),
		'twitter.consumersecret' => $registry->get('twitter.consumersecret'),
		'twitter.requesttoken' => $registry->get('twitter.requesttoken'),
		'twitter.requesttokensecret' => $registry->get('twitter.requesttokensecret'),
		'twitter.pin' => $registry->get('twitter.pin'),
		'twitter.accesstoken' => $registry->get('twitter.accesstoken'),
		'twitter.accesstokensecret' => $registry->get('twitter.accesstokensecret'),
	);

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
