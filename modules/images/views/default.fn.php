<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	find and display the default image of some object
//--------------------------------------------------------------------------------------------------
//arg: refUID - alias or UID of owner object [string]
//opt: altUser - UID of a Users_User object [string]
//opt: size - width100, width200, width300, width570, thumb, thumbsm or thumb90 [string]
//opt: link - link to larger image (yes|no) [string]
//: if there is no default image, a user's default image may be used instead by filling altUser
//TODO: add refModule and refModel checks

function images_default($args) {
	global $kapenta, $db;
	$size = 'width300';
	$link = 'yes';
	$altUser = '';
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	//TODO: check permissions
	if (false == array_key_exists('refUID', $args)) { return false; }
	if (true == array_key_exists('link', $args)) { $link = $args['link']; }
	if (true == array_key_exists('altUser', $args)) { $altUser = $args['altUser']; }

	if (array_key_exists('size', $args)) {	// TODO - make this a setting
		if ($args['size'] == 'thumb') { $size = 'thumb'; }
		if ($args['size'] == 'thumbsm') { $size = 'thumbsm'; }
		if ($args['size'] == 'thumb90') { $size = 'thumb90'; }
		if ($args['size'] == 'width100') { $size = 'width100'; }
		if ($args['size'] == 'width200') { $size = 'width200'; }
		if ($args['size'] == 'width300') { $size = 'width300'; }
		if ($args['size'] == 'width570') { $size = 'width570'; }
	}
	
	//----------------------------------------------------------------------------------------------
	//	find default (lowest weight image), if any
	//----------------------------------------------------------------------------------------------
	$conditions = array("refUID='" . $db->addMarkup($args['refUID']) . "'");
	$range = $db->loadRange('images_image', '*', $conditions, 'weight ASC', '1');

	if (0 == count($range)) {
		if (('' == $altUser) || (false == $db->objectExists('users_user', $altUser))) {
			//--------------------------------------------------------------------------------------
			// no images found for this item
			//--------------------------------------------------------------------------------------
			$imgUrl = $kapenta->serverPath . 'themes/clockface/unavailable/' . $size . '.jpg';
			$html = "[[:images::unavailable::size=" . $size . ":]]"; 

		} else {
			//--------------------------------------------------------------------------------------
			// try the user's image
			//--------------------------------------------------------------------------------------
			$html = '[[:images::default::size=' . $size. '::link=no::refUID=' . $altUser . ':]]';
		}

	} else {
		//-----------------------------------------------------------------------------------------
		//	item owns at least one image, return default (the one with lowest weight)
		//-----------------------------------------------------------------------------------------
		$row = array_pop($range);
		$imgUrl = "%%serverPath%%images/s_" . $size . "/" . $row['alias'];
		$html = "<img src='" . $imgUrl . "' border='0' alt='" . $row['title'] . "' />";

		if ($link == 'yes') {
			//-------------------------------------------------------------------------------------
			// image with link to self on images module
			//-------------------------------------------------------------------------------------
			$fullUrl = '%%serverPath%%images/show/' . $row['alias'];
			$html = "<a href='" . $fullUrl . "'>$html</a>";
		}
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
