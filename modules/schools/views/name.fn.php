<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return a school's name
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Schools_School object [string]
//opt: schoolUID - overrides raUID [string]
//opt: link - link to this record? [string]

function schools_name($args) {
	global $db;
	$link = 'no';
	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('schoolUID', $args)) { $args['raUID'] = $args['schoolUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }
	if (true == array_key_exists('link', $args)) { $link = $args['link']; }
	$model = new Schools_School($db->addMarkup($args['raUID']));	
	if (false == $model->loaded) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	if ('no' == $link) { $html = $model->name; }
	else { $html = "<a href='%%serverPath%%schools/" . $model->alias . "'>". $model->name ."</a>"; }
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

