<?

	require_once($installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	tags (no args)
//--------------------------------------------------------------------------------------------------
//arg: fileUID - overrides raUID [string]
//arg: raUID - recordAlias or UID [string]

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

