<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	user search results  // TODO: pagination, tidy this up
//--------------------------------------------------------------------------------------------------
//arg: q - query [string]
//opt: b64 - set to 'yes' if q is base64 encoded (yes|no) [string]
//opt: mode - adds additional options to search results (friend) [string]
//opt: pageno - page number (not yet implemented) [string]
//opt: cbjs - javascript callback function for when result is clicked [string]
//opt: cblabel - alt text of result button [string]
//opt: cbicon - image to use for result button [string]

function users_search($args) {
	global $db, $user, $theme;

	$query = '';						//%	search terms [string]
	$num = 10;							//%	number of results per page [int]
	$start = 0;							//%	first result in recordset to show [int]
	$pageno = 1;						//%	current result page (1 -> n) [int]
	$cbjs = '';							//%	javascript function to add to result button [string]
	$cblabel = '';						//%	label of result button [string]
	$cbicon = 'arrow_left_green.png';	//%	image for result button [string]
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return "[[:users:plaselogin:]]"; }
	if (false == array_key_exists('q', $args)) { return ''; }
	if (true == array_key_exists('cbjs', $args)) { $cbjs = $args['cbjs']; }
	if (true == array_key_exists('cblabel', $args)) { $cblabel = $args['cblabel']; }
	if (true == array_key_exists('cbicon', $args)) { $cbicon = $args['cbicon']; }

	$query = trim($args['q']);
	
	if (true == array_key_exists('b64', $args)) { $query = trim(base64_decode($query)); }

	$query = $theme->stripBlocks($query);
	if ('' == trim($query)) { return ''; }
	if (1 == strlen($query)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$parts = explode(' ', strtolower($query));
	$qsField = "concat(firstname, ' ', surname, ' ', username)";

	$conditions = array();
	$conditions[] = "role <> 'banned'";
	$conditions[] = "role <> 'public'";

	foreach($parts as $part) {
		if ('' != $part) { $conditions[] = "LOCATE('". $db->addMarkup($part) ."', $qsField) > 0"; }
	}

	$totalItems = $db->countRange('users_user', $conditions);
	$range = $db->loadRange(
		'users_user', "UID, $qsField as qs",
		$conditions, 'surname', $num, $start
	);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$html .= "<small><b>$totalItems users match your query.</b></small><br/>";

	foreach($range as $item) {
		if ('' == $cbjs) {
			$html .= "[[:users::summarynav::userUID=" . $item['UID'] . ":]]\n";

		} else {
			$html .= "
			<table noborder width='100%'>
				<tr>
					<td valign='top'>
						<a 
							href='javascript:void(0)' 
							onClick=\"" . $cbjs . "('" . $item['UID'] . "')\" 
							title=\"" . $cblabel. "\"
						>
						<img 
							src='%%serverPath%%themes/%%defaultTheme%%/icons/" . $cbicon . "' 
							alt=\"" . $cblabel . "\" 
							border='0' 
						/></a>
					</td valign='top'>
					<td>
						[[:users::summarynav::userUID=" . $item['UID'] . ":]]
						<div id='divSRStatus" . $item['UID'] . "'>
					</td>
				</tr>
			</table>
			";
		}
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
