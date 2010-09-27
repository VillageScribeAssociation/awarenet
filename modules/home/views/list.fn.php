<?

	require_once($kapenta->installPath . 'modules/home/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary list (list of all static pages)
//--------------------------------------------------------------------------------------------------
//opt: page - page no to display (default is 1) [string]
//opt: num - number of records per page (default 300) [string]

function home_list($args) {
	global $db, $page, $theme, $user;
	$num = 300;							//%	number of items per page [int]
	$pageNo = 1;							//%	page number starts at 1 [int]
	$start = 0;							//%	starting position within SQL results [int]
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('home', 'Home_Static', 'show', '')) { return ''; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('page', $args)) { 
		$pageNo = (int)$args['page']; 
		$start = ($pageNo - 1) * $num;
	}

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$list = $db->loadRange('Home_Static', '*', '', 'title', $num, $start);
	
	//TODO: use $theme->arrayToHtmlTable();
	$html = "<table noborder>\n";
	if ($user->authHas('home', 'Home_Static', 'edit')) {
		$html .= "<tr><td class='title'>UID</td><td class='title'>Page</td></tr>";
	} else {
		$html .= "<tr><td class='title'>UID</td><td class='title'>Page</td>" 
		      . "<td class='title'>Edit</td><td class='title'>Delete</td></tr>";
	}
	
	$block = $theme->loadBlock('modules/home/views/list.block.php');

	foreach($list as $UID => $row) {
		$model = new Home_Static();
		$model->loadArray($row);
		$html .= $theme->replaceLabels($model->extArray(), $block);
	}  
	$html .= "</table>";
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
