<?

	require_once($installPath . 'modules/files/models/files.mod.php');

//--------------------------------------------------------------------------------------------------
//	return an file tag for a thumbnail
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID of record
// * $args['link'] = link to file page (yes|no)

function files_thumb($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$i = new file($args['raUID']);
	if ($i->data['fileName'] == '') { return false; }
	$html = "<img src='/files/thumb/" . $i->data['recordAlias'] . "' border='0' />";
	if ((array_key_exists('link', $args)) AND ($args['link'] == 'yes')) {
		$html = "<a href='/files/show/" . $i->data['recordAlias'] . "'>$html</a>";
	}
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>