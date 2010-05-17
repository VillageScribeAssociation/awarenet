<?

	require_once($installPath . 'modules/users/models/friendship.mod.php');
	require_once($installPath . 'modules/users/models/user.mod.php');

//-------------------------------------------------------------------------------------------------
//|	select box for choosing a user // TODO: security consideration of access to this
//-------------------------------------------------------------------------------------------------
//arg: default - default value (UID of a user), current user if not supplied [string]
//arg: varname - field name, default is 'user' [string]

function users_select($args) {
	global $user;
	$varname = 'user';
	$default = $user->data['UID'];
	if (array_key_exists('varname', $args) == true) { $varname = $args['varname']; }
	if (array_key_exists('default', $args) == true) { $default = $args['default']; }

	$html = "<select name='" . $varname . "'>\n";
	$sql = "select UID, firstname, surname, username, recordAlias "
		 . "from users order by firstname, surname";

	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
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
