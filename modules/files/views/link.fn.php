<?

	require_once($installPath . 'modules/files/models/files.mod.php');

//--------------------------------------------------------------------------------------------------
//	make a link to a file
//--------------------------------------------------------------------------------------------------
// * $args['fileUID'] = overrides raUID
// * $args['raUID'] = recordAlias or UID
function files_link($args) {
	if (array_key_exists('fileUID', $args)) { $args['raUID'] = $args['fileUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$f = new File($args['raUID']);
	$html = "<a href='/files/dn/" . $f->data['recordAlias'] . "' title='" . $f->data['title'] . "'>" 
	      . $f->data['title'] . "</a>";
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>