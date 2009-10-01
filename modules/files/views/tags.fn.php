<?

	require_once($installPath . 'modules/files/models/files.mod.php');

//--------------------------------------------------------------------------------------------------
//	tags (no args)
//--------------------------------------------------------------------------------------------------
// * $args['fileUID'] = overrides raUID
// * $args['raUID'] = recordAlias or UID

function files_tags($args) {
	if (array_key_exists('fileUID', $args)) { $args['raUID'] = $args['fileUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }

	$html = '';
	$html .= "<b>Tag:</b> &#91;&#91;:files::link::fileUID=" 
			. $args['raUID'] . ":&#93;&#93;<br/>\n";
	$html .= "<b>Tag:</b> &#91;&#91;:files::dnbox::fileUID=" . $args['raUID'] 
			. ":&#93;&#93;<br/>\n";
	$html .= "<b>Tag:</b> &#91;&#91;:files::dnboxnav::fileUID=" . $args['raUID'] 
			. ":&#93;&#93;<br/>\n";
	return $html;
}


?>