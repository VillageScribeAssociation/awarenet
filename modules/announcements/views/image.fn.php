<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	find the first picture on the announcement (if there is one) or return info icon
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of an Announcements_Announcement object [string]
//arg: size - 100, 200, 300, 570, thumb or thumb90 [string]
//opt: announcementUID - overrides raUID [string]
//opt: link - link to larger image (yes|no) [string]

function announcements_image($args) {
	global $db, $kapenta;
	$size = 'width300';
	$link = 'yes';
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
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
	
	$model = new Announcements_Announcement($args['raUID']);
	if (false == $model->loaded) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$html = "[[:images::default::link=no::size=" . $size
			. "::refModule=announcements::refModel=Announcements_Annoucement"
			. "::refUID=" . $model->UID . "::altUser=" . $model->createdBy . ":]]";
	     
	
	if ($link == 'yes') { $html = "<a href='/images/show/" . $row['alias'] . "'>$html</a>"; }
	
	//TODO: setup default 'fail' image

	return $html; 
}


?>
