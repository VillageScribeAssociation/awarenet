<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display upload/view for a single image (eg, user profile picture)
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a module [string]
//arg: refModel - object type [string]
//arg: refUID - UID of object which owns image [string]
//opt: category - category of image, eg userprofile [string]
//opt: width - width of image (?) [string]

function images_uploadsingle($args) {
		global $kapenta;
		global $kapenta;
		global $theme;
		global $kapenta;

	$html = '';					//%	return value [string]
	$refModule = '';			//%	a kapenta module name [string]
	$refModel = '';				//%	a model type name [string]
	$refUID = '';				//%	UID of object which owns this [string]
	$category = '';				//%	no default category [string]
	$width = '300';				//%	default width to display current image at [int]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	if (false == array_key_exists('refModule', $args)) { return ''; }
	if (false == array_key_exists('refModel', $args)) { return ''; }
	if (false == array_key_exists('refUID', $args)) { return ''; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { return ''; }
	if (false == $kapenta->db->tableExists($refModel)) { return ''; }
	if (false == $kapenta->db->objectExists($refModel, $refUID)) { return ''; }

	if (false == $kapenta->user->authHas($refModule, $refModel, 'images-add', $refUID)) { return ''; }
	//TODO: check permission for uploading images

	if (true == array_key_exists('category', $args)) { $category = $args['category']; }
	if (true == array_key_exists('width', $args)) { $width = $args['width']; }

	//----------------------------------------------------------------------------------------------
	//	add block
	//----------------------------------------------------------------------------------------------
	$labels = array();
	$labels['refModule'] = $refModule;
	$labels['refModel'] = $refModel;
	$labels['refUID'] = $refUID;

	$block = $theme->loadBlock('modules/images/views/uploadsingle.block.php');
	$html = $theme->replaceLabels($labels, $block);
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
