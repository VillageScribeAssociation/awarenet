<?

	require_once($installPath . 'modules/static/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary list (list of all static pages)
//--------------------------------------------------------------------------------------------------------------
//opt: page - page no to display (default is 1) [string]
//opt: num - number of records per page (default 300) [string]

function static_list($args) {
	if (authHas('static', 'view', '') == false) { return false; }

	//------------------------------------------------------------------------------------------------------		
	//	arguments
	//------------------------------------------------------------------------------------------------------
	$start = 0; $num = 300; $page = 1;

	if (array_key_exists('num', $args)) { $num = $args['num']; }
	if (array_key_exists('page', $args)) { 
		$page = $args['page']; 
		$start = ($page - 1) * $num;
	}

	//------------------------------------------------------------------------------------------------------		
	//	query database
	//------------------------------------------------------------------------------------------------------
	$list = dbLoadRange('static', '*', '', 'title', $num, $start);
	
	$html = "<table noborder>\n";
	if (authHas('static', 'edit', '')) {
		$html .= "<tr><td class='title'>UID</td><td class='title'>Page</td></tr>";
	} else {
		$html .= "<tr><td class='title'>UID</td><td class='title'>Page</td>" 
		      . "<td class='title'>Edit</td><td class='title'>Delete</td></tr>";
	}
	
	foreach($list as $UID => $row) {
		$model = new StaticPage();
		$model->loadArray($row);
		$html .= replaceLabels($model->extArray(), loadBlock('modules/static/views/list.block.php'));
	}  
	$html .= "</table>";
	$blockEd = "<small><a href='/blocks/edit/module_static/list.block.php'>[edit block]</a></small>";
	$html = str_replace($blockEd, '', $html);
	
	return $html;
}

//--------------------------------------------------------------------------------------------------------------

?>
