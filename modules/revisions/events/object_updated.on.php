<?

	require_once($kapenta->installPath . 'modules/p2p/models/gift.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/offers.set.php');

//--------------------------------------------------------------------------------------------------
//|	fired when an object is saved to database
//--------------------------------------------------------------------------------------------------
//arg: module - module which owned the deleted object [string]
//arg: model - type of deleted object [string]
//arg: UID - UID of deleted object [string]
//arg: data - set of fields and values that were saved [dict]
//arg: changes - set of fields and values which are different to last version [dict]
//arg: dbSchema - database table definition [array]

function revisions__cb_object_updated($args) {
	global $kapenta;
	global $db; 
	global $user;
	global $page;
	global $session;
	global $revisions;

	if (false == array_key_exists('module', $args)) { return false; }
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }
	if (false == array_key_exists('data', $args)) { return false; }
	if (false == array_key_exists('dbSchema', $args)) { return false; }

	if (false == array_key_exists('editedOn', $args['data'])) { return false; }
	if ('revisions_revision' == $args['model']) { return false; }	// prevent infinite recursion

	//----------------------------------------------------------------------------------------------
	//	if this is a revisions_deleted object, make sure the item was actually deleted
	//----------------------------------------------------------------------------------------------
	//	note that this is important when receiving revisions_deleted objects from other peers.

	if ('revisions_deleted' == $args['model']) {
		if (true == $db->objectExists($args['data']['refModel'], $args['data']['refUID'])) {
			if (true == $db->tableExists($args['data']['refModel'])) {
				echo "event deletion of " . $args['data']['refModel'] . '::' . $args['data']['refUID'] . "<br/>";
				$dbSchema = $db->getSchema($args['data']['refModel']);
				$db->delete($args['data']['refUID'], $dbSchema);
			}		
		}
	}

	return true;
}

//--------------------------------------------------------------------------------------------------

?>
