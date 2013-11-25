<?

	require_once($kapenta->installPath . 'modules/revisions/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//|	Fired when an object is saved to database
//--------------------------------------------------------------------------------------------------
//;	Note that revisions are not made for all objects or all circumstances, and that this behavior
//;	can be overridden in several ways:
//;
//;		* by by setting 'archive' member of dbSchema to 'no',
//;		* by adding all fields to 'nodiff' in dbSchema (to not compare for revision),
//;		* by setting the 'revision' argument of $db->save(...) to false,
//;	
//;	In addition, default behavior specified by modules can be overridden by registry settings.  One
//;	can set 'revisions.enabled' to 'no' in the registry to disable this module entirely, or create
//;	a key called 'revisions.limitto' to select which modules and models are allowed (comma sparated
//;	list).
//;
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
	global $kapenta;
	global $session;

	if ((true == array_key_exists('revision', $args)) && ('no' == $args['revision'])) {
		return false;						//	caller has requested revision not be made
	}

	if (false == array_key_exists('module', $args)) { return false; }
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }
	if (false == array_key_exists('data', $args)) { return false; }
	if (false == array_key_exists('dbSchema', $args)) { return false; }

	if (false == array_key_exists('editedOn', $args['data'])) { return false; }
	if ('revisions_revision' == $args['model']) { return false; }	// prevent infinite recursion

	$refModule = $args['module'];			//%	module which updated object belongs to [string]
	$refModel = $args['model'];				//%	type of updated object [string]
	$refUID = $args['UID'];					//%	UID of updated object [string]
	$dbSchema = $args['dbSchema'];			//%	table definition [array]
	$changes = $args['changes'];			//%	dict of fields which have changed [array]
	$data = $args['data'];					//%	dict of all fields and values [array]

	//----------------------------------------------------------------------------------------------
	//	check if revisions to obejcts of this type are disallowed by registry settings
	//----------------------------------------------------------------------------------------------

	if ('no' == $kapenta->registry->get('revisions.enabled')) { return false; }
	$csv = $kapenta->registry->get('revisions.limitto');

	if ('' != $csv) {
		$allow = explode(',', $csv);		//%	names of modules or models [array]
		$found = false;						//%	revisions kept for this [string]

		foreach($allow as $compare) {
			if ((trim($compare) == $refModule) || (trim($compare) == $refModel)) { $found = true; }
		}

		if (false == $found) { return false; }
	}

	//----------------------------------------------------------------------------------------------
	//	if this is a revisions_deleted object, make sure the item was actually deleted
	//----------------------------------------------------------------------------------------------
	//	note that this is important when receiving revisions_deleted objects from other peers.

	if (
		('revisions_deleted' == $refModel) &&
		(true == $db->tableExists($data['refModel'])) &&
		(true == $db->objectExists($data['refModel'], $data['refUID'])) &&
		('delete' == $data['status'])
	) {
		$dbSchema = $db->getSchema($data['refModel']);
		$db->delete($data['refUID'], $dbSchema);
	}

	//----------------------------------------------------------------------------------------------
	//	bail if revisions are not kept for any of the changed fields
	//----------------------------------------------------------------------------------------------
	if (false == $db->checkSchema($dbSchema)) {
		$session->msgAdmin('Could not save revision, invalid schema.', 'bad');
		return false;
	}

	if (false == array_key_exists('archive', $dbSchema)) { $dbSchema['archive'] = 'yes'; }
	if ('no' == $dbSchema['archive']) { return false; }			//	this type is not revisioned

	//----------------------------------------------------------------------------------------------
	//	make sure there is a 'nodiff' field set
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('nodiff', $dbSchema)) {
		$dbSchema['nodiff'] = array();							//	by default all are revisioned
		if (true == array_key_exists('diff', $dbSchema)) {
			//--------------------------------------------------------------------------------------
			//	invert legacy diff array
			//--------------------------------------------------------------------------------------
			foreach($dbSchema['fields'] as $fName => $fType) {
				if (false == in_array($fName, $dbSchema['diff'])) {
					$dbSchema['nodiff'][] = $fName;
				}
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	abort if no revisioned fields have changed in this update
	//----------------------------------------------------------------------------------------------
	$significant = array();

	foreach($changes as $key => $value) {
		if (false == in_array($key, $dbSchema['nodiff'])) {
			$significant[$key] = $value;
		}
	}

	if (0 == count($significant)) { return false; }				//	nothing to make note of

	//----------------------------------------------------------------------------------------------
	//	store the revision
	//----------------------------------------------------------------------------------------------
	$model = new Revisions_Revision();
	$model->refModule = $refModule;
	$model->refModel = $refModel;
	$model->refUID = $refUID;
	$model->fields = $significant;
	$report = $model->save();

	if ('' != $report) {
		$session->msgAdmin("Could not save revision:<br/>\n" . $report, 'bad');
		return false;
	} else {
		$session->msgAdmin("Revision created.", 'ok');
	}

	return true;
}

//--------------------------------------------------------------------------------------------------

?>
