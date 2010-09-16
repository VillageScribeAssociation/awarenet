<?

//--------------------------------------------------------------------------------------------------
//|	form for adding annotations to abuse reports
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of an Abuse_Report object [string]

function abuse_annotationform($args) {
	global $user;
	//global $db;
	$html = '';													//%	return value [string]
	if ('admin' != $user->data['ofGroup']) { return ''; }
	//if ('admin' != $user->role) { return ''; }
	if (false == array_key_exists('UID', $args)) { return ''; }
	//if (false == $db->objectExists('Abuse_Report', $args['UID'])) { return ''; }

	$labels = array('UID' => $args['UID']);
	$html = replaceLabels($labels, loadBlock('modules/abuse/views/annotationform.block.php'));
	return $html;
}

?>
