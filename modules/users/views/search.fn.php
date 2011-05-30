<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	user search results  // TODO: pagination, fix this up
//--------------------------------------------------------------------------------------------------
//arg: q - query [string]
//opt: b64 - set to 'yes' if q is base64 encoded (yes|no) [string]
//opt: mode - adds additional options to search results (friend) [string]
//opt: pageno - page number (not yet implemented) [string]

function users_search($args) {
	global $db, $user, $theme;
	$html = '';		//%	return value [string]
	$query = '';	//%	search terms [string]
	$num = 10;
	$start = 0;
	$pageno = 1;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return "[[:users:plaselogin:]]"; }
	if (false == array_key_exists('q', $args)) { return ''; }

	$query = trim($args['q']);
	
	if (true == array_key_exists('b64', $args)) { $query = trim(base64_decode($query)); }

	$query = $theme->stripBlocks($query);
	if ('' == trim($query)) { return ''; }
	if (1 == strlen($query)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$parts = explode(' ', strtolower($query));
	$qsField = "concat(firstname, ' ', surname, ' ', username)";

	$conditions = array();
	foreach($parts as $part) {
		if ('' != $part) { $conditions[] = "LOCATE('". $db->addMarkup($part) ."', $qsField) > 0"; }
	}

	$totalItems = $db->countRange('users_user', $conditions);
	$range = $db->loadRange('users_user', "UID, $qsField as qs", $conditions, 'surname', $num, $start);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$html .= "<small><b>$totalItems users match your query.</b></small><br/>";

	foreach($range as $item) {
		$html .= "[[:users::summarynav::userUID=" . $item['UID'] . ":]]\n";
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
