<?

//--------------------------------------------------------------------------------------------------
//*	list other users who like some object
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of liked object [string]
//arg: refUID - UID of liked object [string[
//opt: userUID - UID of a user who likes this object [string]

function like_otherusers($args) {
	global $kapenta;
	global $kapenta;
	global $theme;

	$userUID = '';						//%	UID of a Users_User object liking this item [string]
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if (('public' == $kapenta->user->role) || ('banned' == $kapenta->user->role)) { return ''; }

	if (false == array_key_exists('refModule', $args)) { return 'refModule not given'; }
	if (false == array_key_exists('refModule', $args)) { return 'refModule not given'; }
	if (false == array_key_exists('refUID', $args)) { return 'refUID not given'; }
	if (true == array_key_exists('userUID', $args)) { $userUID = $args['userUID']; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModule='" . $kapenta->db->addMarkup($refModule) . "'";
	$conditions[] = "refModel='" . $kapenta->db->addMarkup($refModel) . "'";
	$conditions[] = "refUID='" . $kapenta->db->addMarkup($refUID) . "'";
	$conditions[] = "createdBy != '" . $kapenta->db->addMarkup($userUID) . "'";
	$conditions[] = "cancelled='no'";

	$range = $kapenta->db->loadRange('like_something', '*', $conditions);
	$num = count($range);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	if (0 == $num) { 
		$html .= "[[:users::namelink::userUID=$userUID:]] is the first to like this item.";
		return $html;
	}

	if (1 == $num) { 
		foreach($range as $item) {
			$html .= "[[:users::namelink::userUID=" . $item['createdBy'] . ":]] also likes this.";
		}
		return $html;
	}

	$i = 1;
	$pics = '';
	$names = '';
	foreach ($range as $item) {
		$i++;
		$names .= "[[:users::namelink::userUID=" . $item['createdBy'] . ":]]";
		$pics .= ''
		 . "[[:images::default"
		 . "::refModule=users::refModel=users_user::refUID=" . $item['createdBy']
		 . "::size=thumbsm::display=inline:]]";

		if (($i == $num) && ($num > 1)) { $names .= " and "; }
		else { if ($num > $i) { $names .= ", "; } }
	}


	$html = "<small>$pics<br/>$names also like this. ($num)</small>";
	return $html;
}

?>
