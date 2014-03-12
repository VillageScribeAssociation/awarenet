<?

//--------------------------------------------------------------------------------------------------
//|	dismiss an abuse report
//--------------------------------------------------------------------------------------------------
//arg: UID - UID fo an abuse report [string]

function abuse_dismissbutton($args) {
		global $kapenta;
		global $theme;

	$html = '';

	if ('admin' != $kapenta->user->role) { return ''; }
	$labels = array('UID' => $args['UID']);
	$block = $theme->loadBlock('modules/abuse/views/dismissbutton.block.php');
	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
