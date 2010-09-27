<?

	require_once($kapenta->installPath . 'modules/static/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary list (list of all static pages)
//--------------------------------------------------------------------------------------------------
//opt: page - page no to display (default is 1) [string]
//opt: num - number of records per page (default 300) [string]

function static_list($args) {
	global $db, $page, $theme, $user;
	$start = 0;
	$num = 300;
	$pageNo = 1;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('home', 'Home_Static', 'show')) { return ''; }
	if (true == array_key_exists('num', $args)) { $num = $args['num']; }
	if (true == array_key_exists('page', $args)) { 
		$pageNo = (int)$args['page']; 
		$start = ($pageNo - 1) * $num;
	}

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$range = $db->loadRange('Home_Static', '*', '', 'title', $num, $start);
	$block = $theme->loadBlock('modules/static/views/list.block.php');

	$html = "<table noborder>\n";
	if ($user->authHas('home', 'Home_Static', 'edit')) {
		$html .= "<tr><td class='title'>UID</td><td class='title'>Page</td></tr>";
	} else {
		$html .= "<tr><td class='title'>UID</td><td class='title'>Page</td>" 
		      . "<td class='title'>Edit</td><td class='title'>Delete</td></tr>";
	}

	//TODO: use $theme->arrayToHtmlTable();

	foreach($range as $UID => $row) {
		$model = new StaticPage();
		$model->loadArray($row);
		$html .= $theme->replaceLabels($model->extArray(), $block);
	}  

	$html .= "</table>";
	$html = str_replace($blockEd, '', $html);

	return $html;
}

//--------------------------------------------------------------------------------------------------------------

?>
