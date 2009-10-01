<?

	require_once($installPath . 'modules/announcements/models/announcements.mod.php');

//--------------------------------------------------------------------------------------------------
//	find the first picture on the announcement (if there is one) or return info icon
//--------------------------------------------------------------------------------------------------
// * $args['announcementUID'] = overrides raUID
// * $args['raUID'] = recordAlias or UID or groups entry
// * $args['size'] = 100, 200, 300, 570, thumb or thumb90
// * $args['link'] = link to larger image (yes|no)

function announcements_image($args) {
	global $serverPath;
	$size = 'width300';
	$link = 'yes';
	if (array_key_exists('announcementUID', $args)) { $args['raUID'] = $args['announcementUID']; }
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
	
	$model = new Announcement(sqlMarkup($args['raUID']));	
	$sql = "select * from images where refModule='announcements' and refUID='" . $model->data['UID'] 
	     . "' order by weight";
	     
	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		if ($link == 'yes') {
			return "<a href='/images/show/" . $row['recordAlias'] . "'>" 
				. "<img src='/images/" . $size . "/" . $row['recordAlias'] 
				. "' border='0' alt='" . $model->data['name'] . "'></a>";
		} else {
			return "<img src='/images/" . $size . "/" . $row['recordAlias'] 
				. "' border='0' alt='" . $p->data['name'] . "'>";
		}
	}
	
	// no images found for this group
	return "<img src='/themes/clockface/images/info.png' border='0'>"; 
}


?>