<?

//--------------------------------------------------------------------------------------------------
//|	create form for selecting schools to roll grade strings for
//--------------------------------------------------------------------------------------------------

function schools_bumpgradeform($args) {
		global $user;
		global $kapenta;
		global $theme;
		global $kapenta;

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return '[[:users::pleaselogin:]]'; }

	//----------------------------------------------------------------------------------------------
	//	load list of schools
	//----------------------------------------------------------------------------------------------
	$conditions = array("hidden='no'");
	$range = $kapenta->db->loadRange('schools_school', '*', $conditions, 'name');

	//----------------------------------------------------------------------------------------------
	//	make the form
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/schools/views/bumpgradeform.block.php');
	$table = array();													//%	[array:array:string]
	$table[] = array('[x]', 'School', 'Last done');

	foreach($range as $item) {
		$cbName = 'cbSchool' . $item['UID'];
		$lastDone = $item['lastBump'];
		if (('' == $lastDone) || ('0000-00-00 00:00:00' == $lastDone)) { $lastDone = '(never)'; }

		$table[] = array("<input type='checkbox' name='$cbName'>", $item['name'], $lastDone);
	}

	$labels['schoolList'] = $theme->arrayToHtmlTable($table, true, true);
	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
