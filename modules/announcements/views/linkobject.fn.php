<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make a link to a referenced object on this module
//--------------------------------------------------------------------------------------------------
//arg: type - type of object being referenced [string]
//arg: UID - UID of referenced object [string]

function announcements_linkobject($args) {
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $args)) { return '(UID not given)'; }
	if (false == array_key_exists('type', $args)) { return '(type not given)'; }

	switch(strtolower($args['type'])) {
		case 'announcements_announcement':
			$model = new Announcements_Announcement($args['UID']);
			if (false == $model->loaded) { 
				$html .= "(not found: " . $args['UID'] . ")";
			} else {
				$ext = $model->extArray();
				$html .= "<a href='" . $ext['viewUrl'] . "'>" . $ext['title'] . "</a>";
			}
			break;

		default:
			$html .= "(unknown type)";
			break;
	}

	return $html;
}

?>

