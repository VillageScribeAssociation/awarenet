<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all most recent x announcments owned by a particular record on a given module
//--------------------------------------------------------------------------------------------------
//opt: pageNo - page to display (int) [string]
//opt: page - legacy, overrrides pageNo if present (int) [string]
//opt: num - number of items per page, default is 10 (int) [string]

function announcements_listall($args) {
	global $kapenta;
	global $db;
	global $theme;
	global $user;

	$pageNo = 1;		//% page number to display, counted from 1 [int]
	$num = 10;			//%	number of announcements per page [int]
	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('pageNo', $args)) { $pageNo = (int)$args['pageNo']; }
	if (true == array_key_exists('page', $args)) { $pageNo = (int)$args['page']; }

	//----------------------------------------------------------------------------------------------
	//	count all announcements
	//----------------------------------------------------------------------------------------------
	$count = $db->countRange('announcements_announcement');
	$totalPages = ceil($count / $num);
	$start = ($pageNo - 1) * $num;

	//----------------------------------------------------------------------------------------------
	//	load requested page
	//----------------------------------------------------------------------------------------------
	$conditions = array();

	$range = $db->loadRange('announcements_announcement', '*', $conditions, 'createdOn DESC', $num, $start);
	$blockFile = 'modules/announcements/views/summary.block.php';

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	if ((0 == count($range)) && (1 == $pageNo)) {
		return "<div class='inlinequote'>No announcements at present.</div>";
	}

	foreach ($range as $row) {
		$html .= "[[:announcements::summary::UID=" . $row['UID'] . ":]]\n";

		//NOTE: the following was removd for brevity, consider re-adding for efficiency sake
		//$model = new Announcements_Announcement();
		//$model->loadArray($row);
		//$html .= $theme->replaceLabels($model->extArray(), $theme->loadBlock($blockFile));
	}  

	if (($num + $start) > $count) { $html .= "<!-- end of results -->\n"; }

	return $html;

}

//--------------------------------------------------------------------------------------------------

?>
