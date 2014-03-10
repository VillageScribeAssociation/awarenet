<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//-------------------------------------------------------------------------------------------------
//|	select box for choosing a user // TODO: security consideration of access to this
//-------------------------------------------------------------------------------------------------
//arg: default - default value (UID of a user), current user if not supplied [string]
//arg: varname - field name, default is 'user' [string]

function users_select($args) {
		global $kapenta;
		global $user;

	$varname = 'user';
	$default = $user->UID;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return ''; }
	if (true == array_key_exists('varname', $args)) { $varname = $args['varname']; }
	if (true == array_key_exists('default', $args)) { $default = $args['default']; }

	$html = "<select name='" . $varname . "'>\n";

	$sql = "select UID, firstname, surname, username, alias "	// TODO: $db->loadRange _
		 . "from users_user order by firstname, surname";

	$result = $kapenta->db->query($sql);
	while ($row = $kapenta->db->fetchAssoc($result)) {
		$row = $kapenta->db->rmArray($row);
		$name = $row['firstname'] . ' ' . $row['surname'];
		$checked = '';
		if ($row['UID'] == $default) { $checked = "checked='checked'"; }
		$html .= "\t<option value='" . $row['UID'] ."' $checked>". $name ."</option>";
	}

	$html .= "</select>";
	return $html;
}

//-------------------------------------------------------------------------------------------------

?>
