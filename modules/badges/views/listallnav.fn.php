<?

//--------------------------------------------------------------------------------------------------
//|	list all  badges formatted for the nav
//--------------------------------------------------------------------------------------------------

function badges_listallnav($args) {
	global $db, $user, $theme;
	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	//TODO: arguments and permissions check

	//----------------------------------------------------------------------------------------------
	//	load badges from the database
	//----------------------------------------------------------------------------------------------
	$conditions = array();									//% to filter list by [array:string]
	//add any conditions here, eg: $conditions[] = "published='yes'";
	$range = $db->loadRange('badges_badge', '*', $conditions, 'name');

	//----------------------------------------------------------------------------------------------
	//	show all badge images
	//----------------------------------------------------------------------------------------------
	$html .= "[[:theme::navtitlebox::label=All Badges:]]\n";
	$imgOpts = "::refModule=badges::refModel=badges_badge::size=thumb::link=no";

	foreach($range as $item) {;
		$imgBlock = '[[:images::default' . $imgOpts . '::refUID=' . $item['UID'] . ':]]';
		$badgeUrl = '%%serverPath%%badges/show/' . $item['alias'];
		$html .= "<a href='$badgeUrl'>$imgBlock</a>";

	}

	$html .= "<br/><br/>\n";

	return $html;
}

?>
