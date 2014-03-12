<?

//--------------------------------------------------------------------------------------------------
//|	displays set of related deletions of an object
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]
//arg: model - type of deleted obejct [string]
//arg: UID - UID of deleted object [string]

function revisions_relateddeletions($args) {
	global $kapenta;
	global $kapenta;
	global $theme;
	global $kapenta;	

	$html = '';								//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }

	if (false == array_key_exists('module', $args)) { return '(module not given)'; }
	if (false == array_key_exists('model', $args)) { return '(model not given)'; }
	if (false == array_key_exists('UID', $args)) { return '(UID not given)'; }

	$module = $args['module'];
	$model = $args['model'];
	$UID = $args['UID'];

	if (false == $kapenta->moduleExists($module)) { return '(unknown module)'; }
	if (false == $kapenta->db->tableExists($model)) { return '(unknown model)'; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModule='" . $kapenta->db->addMarkup($module) . "'";
	$conditions[] = "refModel='" . $kapenta->db->addMarkup($model) . "'";
	$conditions[] = "refUID='" . $kapenta->db->addMarkup($UID) . "'";

	$range = $kapenta->db->loadRange('revisions_deleted', '*', $conditions, 'createdOn DESC');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$table = array();
	$table[] = array('UID', 'Deleted', 'Shared');
	
	foreach($range as $item) {
		$showUrl = '%%serverPath%%revisions/showdeleted/' . $item['UID'];
		$showLink = "<a href='" . $showUrl . "'>" . $item['UID'] . "</a>";
		$table[] = array($showLink, $item['createdOn'], $item['shared']);
	}

	$html = $theme->arrayToHtmlTable($table, true, true);
	return $html;
}

?>
