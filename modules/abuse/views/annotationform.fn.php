<?

//--------------------------------------------------------------------------------------------------
//|	form for adding annotations to abuse reports
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of an Abuse_Report object [string]

function abuse_annotationform($args) {
		global $kapenta;
		global $kapenta;
		global $theme;

	$html = '';													//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }
	if (false == array_key_exists('UID', $args)) { return ''; }

	$model = new Abuse_Report($args['UID']);
	if (false == $model->loaded) { return ''; }
	//TODO: permissions check here, allow for moderator role

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$labels = array('UID' => $model->UID);
	//TODO: more labels?
	$block = $theme->loadBlock('modules/abuse/views/annotationform.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

?>
