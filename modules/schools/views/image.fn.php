<?

	require_once($installPath . 'modules/schools/models/schools.mod.php');

//--------------------------------------------------------------------------------------------------
//	find the school's logo/picture (300px) or a blank image
//--------------------------------------------------------------------------------------------------
// * $args['schoolUID'] = overrides raUID
// * $args['raUID'] = recordAlias or UID or schools entry
// * $args['size'] = 100, 200, 300, 570, thumb or thumb90
// * $args['link'] = link to larger image (yes|no)

function schools_image($args) {
	global $serverPath;
	$size = 'width300';
	$link = 'yes';
	if (array_key_exists('schoolUID', $args)) { $args['raUID'] = $args['schoolUID']; }
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
	
	$model = new School(sqlMarkup($args['raUID']));	
	$sql = "select * from images where refModule='schools' and refUID='" . $model->data['UID'] 
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
	
	if ($size == 'thumb90') {	return "<img src='/themes/clockface/images/nophoto100.jpg' border='0'>"; }
	if ($size == 'thumb') {	return "<img src='/themes/clockface/images/nophoto100.jpg' border='0'>"; }
	if ($size == 'width100') {	return "<img src='/themes/clockface/images/nophoto100.jpg' border='0'>"; }
	if ($size == 'width200') {	return "<img src='/themes/clockface/images/nophoto200.jpg' border='0'>"; }
	if ($size == 'width300') {	return "<img src='/themes/clockface/images/nophoto300.jpg' border='0'>"; }

}

//--------------------------------------------------------------------------------------------------------------

?>