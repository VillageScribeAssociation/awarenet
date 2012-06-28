<?

	require_once($kapenta->installPath . 'modules/chat/models/rooms.set.php');

//--------------------------------------------------------------------------------------------------
//|	make an HTML select listing all chat rooms by UID
//--------------------------------------------------------------------------------------------------
//opt: default - UID of preselected element [string]
//opt: fieldName - HTML form field name to use, default is 'room' [string]


function chat_selectroom($args) {
	global $user;

	$fieldName = 'room';				//%	for field name [string]
	$default = '';						//%	UID of preselected item [string]
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return '(admins only)'; }
	if (true == array_key_exists('fieldName', $args)) { $fieldName = $args['fieldName']; }
	if (true == array_key_exists('default', $args)) { $default = $args['default']; }

	$set = new Chat_Rooms(true);
	if (false == $set->loaded) { return '(could not load rooms)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$html .= "<select name='" . $fieldName . "'>\n";
	foreach($set->members as $item) {
		$ps = '';
		if ($item['UID'] == $default) { $ps .= " selected='SELECTED'"; }
		$html .= "\t<option value='" . $item['UID'] . "'>" . $item['title'] . "</option>\n";
	}	
	$html .= "</select>";

	return $html;
}

?>
