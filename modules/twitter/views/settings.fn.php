<?

//--------------------------------------------------------------------------------------------------
//|	twitter settings form
//--------------------------------------------------------------------------------------------------

function twitter_settings($args) {
		global $theme;
		global $user;
		global $kapenta;

	$html = '';			//%	return value [string:html]

	//----------------------------------------------------------------------------------------------
	//	check that user is an admin
	//----------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/twitter/views/settings.block.php');

	$labels = array(
		'twitter.consumerkey' => $kapenta->registry->get('twitter.consumerkey'),
		'twitter.consumersecret' => $kapenta->registry->get('twitter.consumersecret'),
		'twitter.requesttoken' => $kapenta->registry->get('twitter.requesttoken'),
		'twitter.requesttokensecret' => $kapenta->registry->get('twitter.requesttokensecret'),
		'twitter.pin' => $kapenta->registry->get('twitter.pin'),
		'twitter.accesstoken' => $kapenta->registry->get('twitter.accesstoken'),
		'twitter.accesstokensecret' => $kapenta->registry->get('twitter.accesstokensecret'),
	);

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
