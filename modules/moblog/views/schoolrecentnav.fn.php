<?

	require_once($installPath . 'modules/moblog/models/moblog.mod.php');
	require_once($installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list recent posts for nav from a particular school
//--------------------------------------------------------------------------------------------------
//arg: schoolUID - UID of a school record [string]
//opt: num - number of posts to show (default is 10) [string]

function moblog_schoolrecentnav($args) {
	global $user;
	$num = 10; $html = '';
	if (array_key_exists('schoolUID', $args) == false) { do404(); }
	if (array_key_exists('num', $args) == true) { $num = $args['num']; }
	if (dbRecordExists('schools', $args['schoolUID']) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	get recent posts from this school
	//----------------------------------------------------------------------------------------------

	$sql = "select UID from moblog " 
		 . "where school='". sqlMarkup($args['schoolUID']) ."' and published='yes'"
		 . "order by createdOn limit " . sqlMarkup($num);

	$result = dbQuery($sql);
	$recentPosts = '';

	if (dbNumRows($result) <= 0) { return false; }
	while ($row = dbFetchAssoc($result)) 
		{ $html .= "[[:moblog::summarynav::postUID=". $row['UID'] .":]]"; }

	//----------------------------------------------------------------------------------------------
	//	assemble the block
	//----------------------------------------------------------------------------------------------

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

