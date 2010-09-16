<?

//--------------------------------------------------------------------------------------------------
//|	shows button for reporting abuse
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - object type [string]
//arg: refUID - UID of object to report [string]

function abuse_button($args) {
	global $user;
	$html = '';			//%	return value [string]
	
	if (false == array_key_exists('refModule', $args)) { return '(no module)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no model)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no UID)'; }

	$labels['refModule'] = $args['refModule'];		// TODO: clean strings
	$labels['refModel'] = $args['refModel'];
	$labels['refUID'] = $args['refUID'];

	$block = loadBlock('modules/abuse/views/button.block.php');
	$html = replaceLabels($labels, $block);
	return $html;
}

?>
