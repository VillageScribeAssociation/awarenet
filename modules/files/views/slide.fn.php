<?

	require_once($kapenta->installPath . 'modules/files/models/files.mod.php');

//--------------------------------------------------------------------------------------------------
//	return an file tag for a slide (560w)
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID of record
// * $args['link'] = link to file page (yes|no)

function files_slide($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$i = new Files_File($args['raUID']);
	if ($i->fileName == '') { return false; }
	$html = "<img src='/files/slide/" . $i->alias . "' border='0' />";
	if ((array_key_exists('link', $args)) AND ($args['link'] == 'yes')) {
		$html = "<a href='/files/show/" . $i->alias . "'>$html</a>";
	}
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>