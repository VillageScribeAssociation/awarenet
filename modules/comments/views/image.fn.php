<?

	require_once($installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	find the first picture on the comment (if there is one) or return info icon
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID of comment  [string]
//opt: size - width100, width200, width300, width570, thumb, thumbsm or thumb90 (default width300) [string]
//opt: link - link to larger image (yes|no) [string]
//opt: commentUID - overrides raUID [string]

function comments_image($args) {
	global $serverPath;
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
	
	$model = new comment(sqlMarkup($args['raUID']));	
	$sql = "select * from images where refModule='comments' and refUID='" . $model->data['UID'] 
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

