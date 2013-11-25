<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//-------------------------------------------------------------------------------------------------
//|	fired when an object is deleted ($db->delete())
//-------------------------------------------------------------------------------------------------
//arg: module - module which owned the deleted record [string]
//arg: model - type of object which was deleted [string]
//arg: UID - UID of deleted object [string]

function aliases__cb_object_deleted($args) {
	global $kapenta;
	global $db;
	global $user;
	global $aliases;

	if (false == array_key_exists('module', $args)) { return false; }
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }
	if (false == array_key_exists('data', $args)) { return false; }
	if (false == array_key_exists('alias', $args['data'])) { return false; }
	if (false == array_key_exists('fields', $args['dbSchema'])) { return false; }
	if (false == array_key_exists('alias', $args['dbSchema']['fields'])) { return false; }

	if ('aliases_alias' == $args['model']) { return false; }

	//----------------------------------------------------------------------------------------------
	//	delete any aliases owned by this record
	//----------------------------------------------------------------------------------------------	
	$aliases->deleteAll($module, $model, $UID);

	if (true == $kapenta->mcEnabled) {
		$aliasKey = 'alias::' . $args['dbSchema']['model'] . '::' . strtolower($data['alias']);
		$kapenta->cacheDelete($aliasKey);

		$redirectKey = 'aliasalt::' . $args['dbSchema']['model'] . '::' . strtolower($data['alias']);
		$kapenta->cacheDelete($aliasKey);
	}
}

//-------------------------------------------------------------------------------------------------
?>
