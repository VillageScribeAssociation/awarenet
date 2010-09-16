<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary list
//--------------------------------------------------------------------------------------------------
//opt: page - page no to display (default is 0) [string]
//opt: num - number of records per page (default is 30) [string]

function announcements_summarylist($args) {
	global $db, $page, $theme, $user;
	$num = 30;							//%	number of items per page [int]
	$pageNo = 1;						//%	starts from 1 [int]
	$start = 0;							//%	position in SQL results [int]
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('announcements', 'Announcements_Announcement', 'show')) 
		{ return ''; }

	if (array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (array_key_exists('page', $args)) { 
		$pageNo = (int)$args['page']; 
		$start = ($pageNo - 1) * $num;
	}

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$range = $db->loadRange('Announcements_Announcement', '*', '', 'createdOn', $num, $start);
	$block = $theme->loadBlock('modules/announcements/views/summary.block.php');

	foreach($range as $UID => $row) {
		$model = new Announcements_Announcement();
		$model->loadArray($row);
		$html .= $theme->replaceLabels($model->extArray(), $block);
	}  

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
