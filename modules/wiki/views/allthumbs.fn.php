<?

	require_once($installPath . 'modules/wiki/models/wiki.mod.php');
	require_once($installPath . 'modules/wiki/models/wikicode.mod.php');
	require_once($installPath . 'modules/wiki/models/wikirevision.mod.php');

//--------------------------------------------------------------------------------------------------
//	all images associated with an article (default is thumbnails)
//--------------------------------------------------------------------------------------------------
// * $args['articleUID'] = overrides raUID
// * $args['raUID'] = recordAlias or UID or wiki entry
// * $args['size'] = size of images

function wiki_allthumbs($args) {
	global $serverPath;
	$size = 'width300';

	if (array_key_exists('articleUID', $args)) { $args['raUID'] = $args['articleUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	if (array_key_exists('size', $args) == true) { $size = $args['size']; }
	
	$model = new Wiki($args['raUID']);	
	$sql = "select * from images where refModule='wiki' and refUID='" . $model->data['UID'] 
	     . "' order by weight";
	
	$html = '';
	
	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) {
		if (dbNumRows($result) > 6) { $size = 'width145'; }
		while ($row = dbFetchAssoc($result)) {
			$row = sqlRMArray($row);
			$html .= "<a href='/images/show/" . $row['recordAlias'] . "'>" 
				. "<img src='/images/" . $size . "/" . $row['recordAlias'] 
				. "' border='0' alt='" . $row['title'] . "'></a>";
		}
	} 
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>