<?

//--------------------------------------------------------------------------------------------------
//|	display random images owned by some object on a given module
//--------------------------------------------------------------------------------------------------
//arg: refModule - a module [string]
//arg: refModel - owner object's type [string]
//arg: refUID - UID of object which may own images [string]
//opt: size - size of image [string]
//opt: limit - maximum number of results to return [string]
//opt: link - link to image's page on images module (yes|no) [string]
//opt: altUser - UID of user, display avatar if no images [string]

function images_randomimages($args) {
		global $kapenta;
		global $theme;
		global $user;

	$size = 'thumbsm'; 
	$limit = 1;
	$link = 'yes';
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permission
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refUID', $args)) { return ''; }
	if (false == array_key_exists('refModule', $args)) { return ''; }
	if (true == array_key_exists('size', $args)) { $size = $args['size']; }
	if (true == array_key_exists('limit', $args)) { $limit = (int)$args['limit']; }
	if (true == array_key_exists('link', $args)) { $link = $args['link']; }

	$refModule = $args['refModule'];
	$refUID = $args['refUID'];

	//---------------------------------------------------------------------------------------------
	//	load the image UIDs
	//---------------------------------------------------------------------------------------------
	$block = "[[:images::randomuids::refModule=$refModule::refUID=$refUID::limit=$limit:]]";
	$uids = $theme->expandBlocks($block, '');
	$uids = explode('|', $uids);

	foreach($uids as $uid) {
		$galleryUrl = '%%serverPath%%gallery/TODO-ALIAS_HERE-RANDOMIMAGES';
		$html .= "[[:images::$size::imageUID=$uid::link=$link:]]";
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
