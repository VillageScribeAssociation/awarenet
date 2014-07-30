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
		//$imgBlock = '[[:images::default' . $imgOpts . '::refUID=' . $item['UID'] . ':]]';

		$imgUrl = ''
		 . '%%serverPath%%images/showdefault'
		 . '/refModule_badges'
		 . '/refModel_badges_badge'
		 . '/refUID_' . $item['UID']
		 . '/s_thumb/';

		$imgBlock = ''
		 . "<img"
		 . " src='$imgUrl'"
		 . " border='0'"
		 . " width='100'"
		 . " height='100'"
		 . " class='rounded'"
		 . " style='background-color: #aaaaaa; display: inline;'"
		 . " />";

		$badgeUrl = '%%serverPath%%badges/show/' . $item['alias'];
		$html .= "<a href='$badgeUrl'>$imgBlock</a>";

	}

	$html .= "<br/><br/>\n";

	return $html;
}

?>
