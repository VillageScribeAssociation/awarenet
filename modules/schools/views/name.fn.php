<?

	require_once($installPath . 'modules/schools/models/schools.mod.php');

//--------------------------------------------------------------------------------------------------
//	return a school's name
//--------------------------------------------------------------------------------------------------------------
// * $args['schoolUID'] = overrides $raUID
// * $args['raUID'] = recordAlias or UID or schools entry
// * $args['link'] = link to this record?

function schools_name($args) {
	$link = 'no';
	if (array_key_exists('schoolUID', $args)) { $args['raUID'] = $args['schoolUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	if (array_key_exists('link', $args) == true) { $link = $args['link']; }
	$model = new School(sqlMarkup($args['raUID']));	
	if ($link == 'no') {
		return $model->data['name'];
	} else {
		return "<a href='/schools/" . $model->data['recordAlias'] . "'>"
			  . $model->data['name'] . "</a>";
	}
}

//--------------------------------------------------------------------------------------------------

?>