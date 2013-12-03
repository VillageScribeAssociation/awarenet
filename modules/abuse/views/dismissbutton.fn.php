<?

//--------------------------------------------------------------------------------------------------
//|	dismiss an abuse report
//--------------------------------------------------------------------------------------------------
//arg: UID - UID fo an abuse report [string]

function abuse_dismissbutton($args) {
	global $user, $theme;
	$html = '';

	if ('admin' != $user->role) { return ''; }
	$labels = array('UID' => $args['UID']);
	$block = $theme->loadBlock('modules/abuse/views/dismissbutton.block.php');
	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
