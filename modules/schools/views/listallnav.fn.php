<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all formatted for nav (300 px wide)
//--------------------------------------------------------------------------------------------------

function schools_listallnav($args) {
	global $db, $user;
	$html = '';

	$sql = "select * from Schools_School order by name";
	$range = $db->loadRange('Schools_School', '*', '', 'name');

	foreach($range as $row) 
		{ $html .= "[[:schools::summarynav::schoolUID=" . $row['UID'] . ":]]"; }

	return $html;
}


//--------------------------------------------------------------------------------------------------

?>
