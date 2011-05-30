<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	find the group's logo/picture (300px) or a blank image
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or groups entry [string]
//opt: userUID - overrides raUID [string]
//opt: size - width100, width200, width300, width570, thumb, etc (default width300) [string]
//opt: link - link to larger image (yes|no) (default is yes) [string]

function users_avatar($args) {
	global $db, $kapenta;
	$size = 'width300';
	$link = 'yes';

	if (array_key_exists('userUID', $args)) { $args['raUID'] = $args['userUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	if (array_key_exists('link', $args) == 'no') { $link = 'no'; }
	if (array_key_exists('size', $args)) {
		if ($args['size'] == 'thumb') { $size = 'thumb'; }
		if ($args['size'] == 'thumbsm') { $size = 'thumbsm'; }
		if ($args['size'] == 'thumb90') { $size = 'thumb90'; }
		if ($args['size'] == 'width100') { $size = 'width100'; }
		if ($args['size'] == 'width200') { $size = 'width200'; }
		if ($args['size'] == 'width300') { $size = 'width300'; }
		if ($args['size'] == 'width570') { $size = 'width570'; }
	}

	$model = new Users_User($db->addMarkup($args['raUID']));	
	$sql = "select * from images_image where refModule='users' and refUID='" 
		 . $db->addMarkup($model->UID)
	     . "' order by weight";

	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) {
		if ($link == 'yes') {
			return "<a href='/images/show/" . $row['alias'] . "'>" 
				. "<img src='/images/" . $size . "/" . $row['alias'] 
				. "' border='0' alt='" . $model->getName() . "'></a>";
		} else {
			return "<img src='/images/" . $size . "/" . $row['alias'] 
				. "' border='0' alt='" . $model->getName() . "'>";
		}
	}
	
	// no images found for this group
	return "<img src='/themes/clockface/unavailable/" . $size . ".jpg' border='0'>"; 
}

//--------------------------------------------------------------------------------------------------

?>
