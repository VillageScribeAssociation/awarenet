<?

	require_once($kapenta->installPath . 'modules/calendar/models/template.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a user's list of templates formatted for nav (300px wide)
//--------------------------------------------------------------------------------------------------

function calendar_listtemplatesnav($args) {
		global $user;
		global $kapenta;
		global $theme;

	$html = '';			//%	return value [string:html]

	//----------------------------------------------------------------------------------------------
	//	check permissions / role
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }
	//TODO: user permission set here

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array("createdBy='" . $user->UID . "'");
	$range = $kapenta->db->loadRange('calendar_template', '*', $conditions, 'title');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	if (0 == count($range)) { return ''; }

	$html = "[[:theme::navtitlebox::label=My Calendar Templates:]]\n";

	foreach($range as $item) {
		$model = new Calendar_Template($item['UID']);
		$ext = $model->extArray();
		$html .= $ext['nameLink'] . "<br/>\n";
	}
	
	$html .= "<hr/>\n";

	return $html;
}

?>
