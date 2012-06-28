<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	find and display the default image of some object
//--------------------------------------------------------------------------------------------------
//arg: refUID - alias or UID of owner object [string]
//opt: altUser - UID of a Users_User object [string]
//opt: size - width100, width200, width300, width570, thumb, thumbsm or thumb90 [string]
//opt: link - link to larger image (yes|no) [string]
//opt: display - css display type (inline|block) [string]
//: if there is no default image, a user's default image may be used instead by filling altUser
//TODO: add refModule and refModel checks

function images_default($args) {
	global $kapenta;
	global $db;

	$size = 'width300';				//%	default size [string]
	$link = 'yes';					//%	link to image (yes|no) [string]
	$altUser = '';					//%	UID of a Users_User object (for fallback image) [string]
	$display = 'block';				//%	css element type [string]
	$style = '';					//%	per-element style [string]
	$html = '';						//%	return value [string]

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
	
	if (true == array_key_exists('display', $args)) { $display = $args['display']; }

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
			$imgUrl = $kapenta->serverPath . 'data/images/unavailable/' . $size . '.jpg';
			$html = "[[:images::unavailable::size=" . $size . "::display=$display:]]"; 

		} else {
			//--------------------------------------------------------------------------------------
			// try the user's image
			//--------------------------------------------------------------------------------------
			$html = ''
			 . '[[:images::default'
			 . '::size=' . $size
			 . '::display=' . $display
			 . '::link=no'
			 . '::refModule=users'
			 . '::refModule=users_user'
			 . '::refUID=' . $altUser
			 . ':]]';
		}

	} else {
		//------------------------------------------------------------------------------------------
		//	item owns at least one image, return default (the one with lowest weight)
		//------------------------------------------------------------------------------------------
		$row = array_pop($range);
		$imgUrl = "%%serverPath%%images/s_" . $size . "/" . $row['alias'];

		if ('inline' == $display) { $style = " style='display: inline;'"; }

		$html = ''
		 . "<img"
		 . " src='" . $imgUrl . "'"
		 . " border='0'"
		 . " alt='" . $row['title'] . "'"
		 . " class='rounded'"
		 . $style
		 . " />";

		if ($link == 'yes') {
			//--------------------------------------------------------------------------------------
			// image with link to self on images module
			//--------------------------------------------------------------------------------------
			$fullUrl = '%%serverPath%%images/show/' . $row['alias'];
			$html = "<a href='" . $fullUrl . "'>$html</a>";
		}
	}

	// for AJAX blocks which are served directly
	$html = str_replace('%%serverPath%%', $kapenta->serverPath, $html);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
