<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');
	require_once($kapenta->installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	find the first image associated with a mobblog post  //TODO: remove this
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or groups entry [string]
//opt: postUID - overrides raUID [string]
//opt: size - width100, width200, width300, width570, thumb, thumbsm or thumb90 (default width300) [string]
//opt: link - link to larger image (yes|no) (default is yes) [string]

function moblog_image($args) {
	global $kapenta, $db;
	$size = 'width300';
	$link = 'yes';
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('postUID', $args)) { $args['raUID'] = $args['postUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }
	if ('no' == array_key_exists('link', $args)) { $link = 'no'; }
	if (array_key_exists('size', $args)) {
		//TODO: use a switch, or load image sizes from Images_Image module
		if ($args['size'] == 'thumb') { $size = 'thumb'; }
		if ($args['size'] == 'thumbsm') { $size = 'thumbsm'; }
		if ($args['size'] == 'thumb90') { $size = 'thumb90'; }
		if ($args['size'] == 'width100') { $size = 'width100'; }
		if ($args['size'] == 'width200') { $size = 'width200'; }
		if ($args['size'] == 'width300') { $size = 'width300'; }
		if ($args['size'] == 'width570') { $size = 'width570'; }
	}
	
	$model = new Moblog_Post($args['raUID']);	
	if (false == $model->loaded) { return ''; }

	$block = "[[:images::default::refModule=moblog::refModel=moblog_post::refUID=" . $model->UID 
			. "::size=$size::altUser=" . $model->createdBy . ":]]";
	
	$html = $block;
	if ($link == 'yes') { $html =  "<a href='/moblog/show/" . $model->alias . "'>$block</a>"; }
	
	return $block;
}

//--------------------------------------------------------------------------------------------------

?>
