<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');
	require_once($kapenta->installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list recent posts for nav from a particular school
//--------------------------------------------------------------------------------------------------
//arg: schoolUID - UID of a school record [string]
//opt: num - number of posts to show (default is 10) [string]

function moblog_schoolrecentnav($args) {
		global $kapenta;
		global $page;
		global $user;

	$num = 10; 						//%	number of recent items to show [int]
	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }
	if (false == array_key_exists('schoolUID', $args)) { return ''; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	//if (false == $kapenta->db->objectExists('schools_school', $args['schoolUID'])) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	get recent posts from this school
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "school='". $kapenta->db->addMarkup($args['schoolUID']) ."'";
	$conditions[] = "published='yes'";

	$range = $kapenta->db->loadRange('moblog_post', '*', $conditions, 'createdOn DESC', $num);
	if (0 == count($range)) { return ''; }

	//$sql = "select UID from moblog " 
	//	 . "where school='". $kapenta->db->addMarkup($args['schoolUID']) ."' and published='yes'"
	//	 . "order by createdOn limit " . $kapenta->db->addMarkup($num);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	foreach ($range as $row) 
		{ $html .= "[[:moblog::summarynav::postUID=". $row['UID'] .":]]"; }

	//TODO: make this more efficient

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
