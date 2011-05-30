<?

	require_once($kapenta->installPath . 'modules/badges/models/badge.mod.php');

//--------------------------------------------------------------------------------------------------
//	list all users who recieved badge
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Bades_Badge object [string]
//opt: badgeUID - overrides raUID if present [string]

function badges_recipients($args) {
	global $db, $user;
	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permisisons
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaslogin:]]'; }	// no public users, for now

	if (true == array_key_exists('badgeUID', $args)) { $args['raUID'] = $args['badgeUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Badges_Badge($args['raUID']);
	if (false == $model->loaded) { return '(unkown badge)'; }	

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array("badgeUID='" . $db->addMarkup($model->UID) . "'");
	$range = $db->loadRange('badges_userindex', '*', $conditions);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	foreach($range as $row) { 
		$rmLink = '';
		if ('admin' == $user->role) {			//TODO: use a permission for this
			$rmUrl = '%%serverPath%%badges/revoke/' . $row['UID'];
			$rmLink = "<a href='$rmUrl'>[revoke]</a>";
		}
		$html .= "[[:users::summarynav::userUID=" . $row['userUID'] . "::extra=$rmLink:]]<br/>\n";	
	}

	if (0 == count($range)) { $html .= "(no recipients yet)"; }

	return $html;
}

?>
