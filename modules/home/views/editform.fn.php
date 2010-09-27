<?

	require_once($kapenta->installPath . 'modules/home/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for editing static pages
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of static page to edit [string]

function home_editform($args) {
	global $theme, $user;

	if (array_key_exists('raUID', $args) == false) { return false; }

	$model = new Home_Static($args['raUID']);
	if (false == $model->loaded) { return false; }
	if (false == $user->authHas('home', 'Home_Static', 'edit', $model->UID)) { return ''; }
	
	$extArray = $model->extArray();
	$labels = array();
	
	foreach($extArray as $key => $val) {
		$val = str_replace('[', '&#91;', $val);			
		$val = str_replace(']', '&#93;', $val);			
		$val = str_replace('<', '&lt;', $val);			
		$val = str_replace('>', '&gt;', $val);
		$labels['p_' . $key] = $val;
	}
	
	return $theme->replaceLabels($labels, $theme->loadBlock('modules/home/views/editform.block.php'));
}


?>
