<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return a school's name
//--------------------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or schools entry [string]
//opt: schoolUID - overrides raUID [string]
//opt: link - link to this record? [string]

function schools_name($args) {
	global $db;
	$link = 'no';
	if (array_key_exists('schoolUID', $args)) { $args['raUID'] = $args['schoolUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	if (array_key_exists('link', $args) == true) { $link = $args['link']; }
	$model = new Schools_School($db->addMarkup($args['raUID']));	
	if ($link == 'no') {
		return $model->name;
	} else {
		return "<a href='/schools/" . $model->alias . "'>"
			  . $model->name . "</a>";
	}
}

//--------------------------------------------------------------------------------------------------

?>

