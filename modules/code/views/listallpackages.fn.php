<?

	require_once($kapenta->installPath . 'modules/code/models/package.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all projects for the nav (no arguments)
//--------------------------------------------------------------------------------------------------

function code_listallpackages($args) {
	global $kapenta;
	$html = '';			//%	return value [string:html]

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$range = $kapenta->db->loadRange('code_package', '*', array(), 'name ASC');

	$html .= "<h1>All Packages</h1>";
	if (0 == count($range)) { $html .= "(none)"; }

	foreach($range as $item) {
		$link = '%%serverPath%%code/showpackage/' . $item['alias'];
		$html .= ''
		 . "<a href='" . $link . "'>"
		 . $item['name']
		 . "</a> "
		 . "<small>(v" . $item['version'] . "." . $item['revision'] . ")</small>"
		 . "<br/><hr/>"; 
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
