<?

//--------------------------------------------------------------------------------------------------
//|	dismiss an abuse report
//--------------------------------------------------------------------------------------------------
//arg: UID - UID fo an abuse report [string]

function abuse_dismissbutton($args) {
	global $user;
	if ('admin' != $user->data['ofGroup']) { return ''; }
	$html = '';

	$labels = array('UID' => $args['UID']);
	$html = replaceLabels($labels, loadBlock('modules/abuse/views/dismissbutton.block.php'));

	return $html;
}

?>
