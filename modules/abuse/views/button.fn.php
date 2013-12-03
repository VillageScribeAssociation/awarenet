<?

//--------------------------------------------------------------------------------------------------
//|	shows button for reporting abuse
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - object type [string]
//arg: refUID - UID of object to report [string]

function abuse_button($args) {
	global $user, $theme;
	$html = '';			//%	return value [string]
	
	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(no module)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no model)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no UID)'; }
	//TODO: permissions check here

	$labels['refModule'] = $args['refModule'];		// TODO: clean strings
	$labels['refModel'] = $args['refModel'];
	$labels['refUID'] = $args['refUID'];

	//----------------------------------------------------------------------------------------------
	//	make the button
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/abuse/views/button.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

?>
