<?

//--------------------------------------------------------------------------------------------------
//|	make contact detail edit form
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Contact_Detail object [string]

function contact_editform($args) {
	global $user, $theme;
	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $args)) { return '(UID not given)'; }
	$model = new Contact_Detail($args['UID']);
	if (false == $model->loaded) { return ''; }
	
	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/contact/views/editform.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);
	return $html;
}

?>
