<?

	require_once($kapenta->installPath . 'modules/code/models/bugs.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/code.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/codeproject.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/coderevision.mod.php');

//--------------------------------------------------------------------------------------------------
//	show an image
//--------------------------------------------------------------------------------------------------
// * $args['docUID'] = recordAlias or UID or code entry, overrides raUID
// * $args['raUID'] = recordAlias or UID or code entry

function code_showimage($args) {
	global $db;

	if (array_key_exists('docUID', $args)) { $args['raUID'] = $args['docUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	global $kapenta;
	$html = '';

	$model = new Code($args['raUID']);
	$sql = "select * from images where refUID='" . $model->data['UID'] . "'";
	$result = $db->query($sql);
	if ($db->numRows($result) > 0) {
		$row = $db->rmArray($db->fetchAssoc($result));
		$html = '[[:images::showfull::raUID=' . $row['UID'] . ':]]';
	} else {
		$html .= "<i>(no image associated with this node)</i>";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>