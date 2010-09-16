<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	find the first picture on the announcement (if there is one) or return info icon
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or groups entry [string]
//arg: size - 100, 200, 300, 570, thumb or thumb90 [string]
//opt: announcementUID - overrides raUID [string]
//opt: link - link to larger image (yes|no) [string]

function announcements_image($args) {
	global $db;

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
	
	$model = new Announcements_Announcement($db->addMarkup($args['raUID']));	
	$sql = "select * from Images_Image where refModule='announcements' and refUID='" . $model->UID 
	     . "' order by weight";
	     
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) {
		if ($link == 'yes') {
			return "<a href='/images/show/" . $row['alias'] . "'>" 
				. "<img src='/images/" . $size . "/" . $row['alias'] 
				. "' border='0' alt='" . $model->name . "'></a>";
		} else {
			return "<img src='/images/" . $size . "/" . $row['alias'] 
				. "' border='0' alt='" . $p->name . "'>";
		}
	}
	
	// no images found for this group
	return "<img src='/themes/clockface/images/info.png' border='0'>"; 
}


?>