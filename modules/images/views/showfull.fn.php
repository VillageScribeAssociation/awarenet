<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	full-page display of an image + caption, etc
//--------------------------------------------------------------------------------------------------
//arg: raUID - alais or UID of an Images_Image obejct [string]
//opt: UID - overrides raUID if present [string]
//opt: imageUID - overrides raUID if present [string]
//opt: imgUID - overrides raUID if present [string]

function images_showfull($args) {
	global $user;
	global $db;
	global $theme;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (true == array_key_exists('imageUID', $args)) { $args['raUID'] = $args['imageUID']; }
	if (true == array_key_exists('imgUID', $args)) { $args['raUID'] = $args['imgUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(no image specified)'; }

	$model = new Images_Image($args['raUID']);
	if (false == $model->loaded) { return '(image not found)'; }
	if ('' == $model->fileName) { return '(invalid or corrupt image)'; }
	
	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$labels = $model->extArray();

	$labels['userEditBlock'] = '';
	if (($model->createdBy == $user->UID) || ('admin' == $user->role)) {
		$labels['rotateBlock'] =  "[[:images::rotatebuttons::imageUID=" . $model->UID . ":]]\n";
		$labels['userEditBlock'] = ''
		 . "<br/><br/>\n"
		 . "[[:theme::navtitlebox::label=Edit::toggle=divEditImage::hidden=yes:]]\n"
		 . "<div id='divEditImage' style='visibility: hidden; display: none;'>\n"
		 . "[[:images::editform::return=show::imageUID=" . $model->UID . ":]]\n"
		 . $labels['rotateBlock']
		 . "<hr/>\n"

		 . "</div>\n<br/>\n";
	}	//TODO: user permission for this

	$block = $theme->loadBlock('modules/images/views/showfull.block.php');
	$html = $theme->replaceLabels($labels, $block);
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
