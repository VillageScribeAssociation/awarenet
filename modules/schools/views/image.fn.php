<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	find the school's logo/picture (300px) or a blank image
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or schools entry [string]
//opt: schoolUID - overrides raUID [string]
//opt: size - width100, width200, width300, width570, thumb, thumbsm or thumb90 [string]
//opt: link - link to larger image (yes|no) [string]
//: deprecated, TODO: remove this and replace blocks with call to images module

function schools_image($args) {
	global $db, $user;
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
	
	$model = new Schools_School($db->addMarkup($args['raUID']));	
	if (false == $model->loaded) { return ''; }

	$sql = "select * from Images_Image where refModule='schools' and refUID='" . $model->UID 
	     . "' order by weight";
	     
	$result = $db->query($sql);

	while ($row = $db->fetchAssoc($result)) {
		if ('yes' == $link) {
			return "<a href='%%serverPath%%images/show/" . $row['alias'] . "'>" 
				. "<img src='%%serverPath%%images/" . $size . "/" . $row['alias'] 
				. "' border='0' alt='" . $model->name . "'></a>";
		} else {
			return "<img src='%%serverPath%%images/" . $size . "/" . $row['alias'] 
				. "' border='0' alt='" . $p->name . "'>";
		}
	}
	
	if ($size == 'thumb90') {	return "<img src='/themes/clockface/images/nophoto100.jpg' border='0'>"; }
	if ($size == 'thumb') {	return "<img src='/themes/clockface/images/nophoto100.jpg' border='0'>"; }
	if ($size == 'width100') {	return "<img src='/themes/clockface/images/nophoto100.jpg' border='0'>"; }
	if ($size == 'width200') {	return "<img src='/themes/clockface/images/nophoto200.jpg' border='0'>"; }
	if ($size == 'width300') {	return "<img src='/themes/clockface/images/nophoto300.jpg' border='0'>"; }

}

//--------------------------------------------------------------------------------------------------

?>
