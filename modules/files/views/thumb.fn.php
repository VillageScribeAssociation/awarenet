<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return an file tag for a thumbnail
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of record [string]
//arg: link - link to file page (yes|no) [string]

function files_thumb($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$i = new Files_File($args['raUID']);
	if ($i->fileName == '') { return false; }
	$html = "<img src='/files/thumb/" . $i->alias . "' border='0' />";
	if ((array_key_exists('link', $args)) AND ($args['link'] == 'yes')) {
		$html = "<a href='/files/show/" . $i->alias . "'>$html</a>";
	}
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

