<?

	require_once($kapenta->installPath . 'modules/wiki/models/wiki.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/wikicode.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/wikirevision.mod.php');

//--------------------------------------------------------------------------------------------------
//|	all images associated with an article (default is thumbnails)
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or wiki entry [string]
//opt: articleUID - overrides raUID [string]
//opt: size - size of images (default is thumb90) [string]

function wiki_allthumbs($args) {
	global $db;
	$size = 'thumb90';
	$html = '';			//%	return value [string]

	if (array_key_exists('articleUID', $args)) { $args['raUID'] = $args['articleUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	if (array_key_exists('size', $args) == true) { $size = $args['size']; }
	
	$model = new Wiki($args['raUID']);	
	$sql = "select * from images_image where refModule='wiki' and refUID='" . $model->UID 
	     . "' order by weight";
	
	
	$result = $db->query($sql);
	if ($db->numRows($result) > 0) {
		if ($db->numRows($result) > 6) { $size = 'width145'; }
		while ($row = $db->fetchAssoc($result)) {
			$row = $db->rmArray($row);
			$html .= "<a href='/images/show/" . $row['alias'] . "'>" 
				. "<img src='/images/" . $size . "/" . $row['alias'] 
				. "' border='0' alt='" . $row['title'] . "'></a>";
		}
	} 
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
