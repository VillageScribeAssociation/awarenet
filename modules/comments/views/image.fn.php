<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	find the first picture on the comment (if there is one) or return info icon
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID of comment  [string]
//opt: size - width100, width200, width300, width570, thumb, thumbsm or thumb90 (default width300) [string]
//opt: link - link to larger image (yes|no) [string]
//opt: commentUID - overrides raUID [string]

function comments_image($args) {
		global $db;
		global $kapenta;

	$size = 'width300';
	$link = 'yes';
	if (array_key_exists('commentUID', $args)) { $args['raUID'] = $args['commentUID']; }
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
	
	$model = new comment($db->addMarkup($args['raUID']));	
	$sql = "select * from images_image where refModule='comments' and refUID='" . $model->UID 
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
	return "<img src='%%serverPath%%themes/%%defaultTheme%%/images/info.png' border='0'>"; 
}


?>
