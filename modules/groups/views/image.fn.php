<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	find the group's logo/picture (300px) or a blank image
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or groups entry [string]
//opt: groupUID - overrides raUID [string]
//opt: size - width100, width200, width300, width570, thumb, thumbsm or thumb90 [string]
//opt: link - link to larger image (yes|no) [string]

function groups_image($args) {
	global $db, $user;
	$size = 'width300';
	$link = 'yes';

	if (array_key_exists('groupUID', $args)) { $args['raUID'] = $args['groupUID']; }
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
	
	$model = new Groups_Group($db->addMarkup($args['raUID']));	
	if (false == $model->loaded) { return ''; }

	$sql = "select * from images_image where refModule='groups' and refUID='" . $model->UID 
	     . "' order by weight";
	     
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) {
		if ($link == 'yes') {
			return "<a href='/images/show/" . $row['alias'] . "'>" 
				. "<img src='/images/" . $size . "/" . $row['alias'] 
				. "' border='0' alt='" . $model->name . "'></a>";
		} else {
			return "<img src='/images/" . $size . "/" . $row['alias'] 
				. "' border='0' alt='" . $model->name . "'>";
		}
	}
	
	// no images found for this group
	return "<img src='/themes/clockface/unavailable/" . $size . ".jpg' border='0'>"; 
}

//--------------------------------------------------------------------------------------------------

?>
