<?

	require_once($kapenta->installPath . 'modules/newsletter/models/category.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to edit a Category object
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Newsletter_Category object [string]
//opt: UID - UID of a Newsletter_Category object, overrides raUID [string]
//opt: categoryUID - UID of a Newsletter_Category object, overrides raUID [string]

function newsletter_editcategoryform($args) {
	global $user;
	global $utils;
	global $theme;

	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	$raUID = '';
	if (true == array_key_exists('UID', $args)) { $raUID = $args['UID']; }
	if (true == array_key_exists('raUID', $args)) { $raUID = $args['raUID']; }
	if (true == array_key_exists('categoryUID', $args)) { $raUID = $args['categoryUID']; }
	if ('' == $raUID) { return ''; }

	$model = new Newsletter_Category($raUID);	//% the object we're editing [object:Newsletter_Category]

	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('newsletter', 'newsletter_category', 'edit', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/newsletter/views/editcategoryform.block.php');
	$labels = $model->extArray();
	$labels['description64'] = $utils->b64wrap($labels['description']);
	// ^ add any labels, block args, etc here

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
