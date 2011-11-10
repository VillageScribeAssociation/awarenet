<?

//--------------------------------------------------------------------------------------------------
//|	informational or warning box, used for session messages, etc
//--------------------------------------------------------------------------------------------------
//opt: msg - message to display [string]
//opt: message - alais of msg [string]
//opt: icon - icon to display (info|warn|ok|bad) [string]

function theme_infobox($args) {
	global $theme;

	$icon = 'info';			//%	icon name [string]
	$msg = '';				//%	message to display to user [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('msg', $args)) { $msg = $args['msg']; }
	if (true == array_key_exists('message', $args)) { $msg = $args['msg']; }
	if (true == array_key_exists('icon', $args)) { $icon = $args['icon']; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('themes/clockface/views/infobox.block.php');
	$labels = array(
		'icon' => $icon,
		'msg' => $msg
	);

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
