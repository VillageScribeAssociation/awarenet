<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or schools entry [string]

function schools_summary($args) {
		global $user;
		global $theme;


	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	$raUID = '';
	if (true == array_key_exists('UID', $args)) { $raUID = $args['UID']; }
	if (true == array_key_exists('raUID', $args)) { $raUID = $args['raUID']; }
	if (true == array_key_exists('schoolUID', $args)) { $raUID = $args['schoolUID']; }
	if ('' == $raUID) { return ''; }

	$model = new Schools_School($raUID);	//% the object we're editing [object:Schools_School]

	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('schools', 'schools_school', 'show', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/schools/views/schoolsummary.block.php');
	$labels = $model->extArray();
	// ^ add any labels, block args, etc here

	$html = $theme->replaceLabels($labels, $block);

	return $html;

	//----------------------------------------------------------------------------------------------
	//	previous block
	//----------------------------------------------------------------------------------------------

	//if (array_key_exists('raUID', $args) == false) { return false; }
	//$model = new Schools_School($db->addMarkup($args['raUID']));	
	//$html = $theme->replaceLabels($c->extArray(), $theme->loadBlock('modules/schools/views/summary.block.php'));
	//return $html;
}

//--------------------------------------------------------------------------------------------------

?>

