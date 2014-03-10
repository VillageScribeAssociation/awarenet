<?

	require_once($kapenta->installPath . 'modules/code/models/package.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list users who have permissions on a given packaghe
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID of a Code_Package object [string]
//opt: packageUID - overrides raUID if present [string]
//opt: UID - overrides raUID if present [string]

function code_listpackageusers($args) {
	global $db;
	global $theme;

	$html = '';			//%	return value [string:html]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('packageUID', $args)) { $args['raUID'] == $args['packageUID']; }
	if (true == array_key_exists('UID', $args)) { $args['raUID'] == $args['UID']; }
	if (false == array_key_exists('raUID', $args)) { return '(package not specified)'; }
	
	$model = new Code_Package($args['raUID']);
	if (false == $model->loaded) { return '(unkown package)'; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------

	$conditions = array();
	$conditions[] = "packageUID='" . $db->addMarkup($model->UID) . "'";
	$range = $db->loadRange('code_userindex', '*', $conditions);

	//----------------------------------------------------------------------------------------------
	//	make the block (html table)
	//----------------------------------------------------------------------------------------------

	$table = array();
	$table[] = array('User', 'Added', 'Permission');

	foreach($range as $item) {
		$userNameBlock = '[[:users::namelink::userUID=' . $item['userUID'] . ':]]';
		$table[] = array($userNameBlock, substr($item['createdOn'], 0, 10), $item['privilege']);
	}

	$html = $theme->arrayToHtmlTable($table, true, true);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
