<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make a link to a referenced object on this module
//--------------------------------------------------------------------------------------------------
//arg: type - type of object being referenced [string]
//arg: UID - UID of referenced object [string]

function groups_linkobject($args) {
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $args)) { return '(UID not given)'; }
	if (false == array_key_exists('type', $args)) { return '(type not given)'; }

	switch(strtolower($args['type'])) {
		case 'groups_group':
			$model = new Groups_Group($args['UID']);
			if (false == $model->loaded) { 
				$html .= "(not found)";
			} else {
				$ext = $model->extArray();
				$html .= "<a href='" . $ext['viewUrl'] . "'>" . $ext['name'] . "</a>";
			}
			break;

		default:
			$html .= "(unknown type)";
			break;
	}

	return $html;
}

?>

