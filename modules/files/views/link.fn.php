<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make a link to a file
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID [string]
//opt: fileUID - overrides raUID [string]

function files_link($args) {
	if (array_key_exists('fileUID', $args)) { $args['raUID'] = $args['fileUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$f = new Files_File($args['raUID']);
	$html = "<a href='/files/dn/" . $f->alias . "' title='" . $f->title . "'>" 
	      . $f->title . "</a>";
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

