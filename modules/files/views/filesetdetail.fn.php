<?

	require_once($installPath . 'modules/files/models/files.mod.php');

//--------------------------------------------------------------------------------------------------
//	display a set of files associated with something, along with some metadata
//--------------------------------------------------------------------------------------------------
// * $args['refModule'] = module to list on
// * $args['refUID'] = number of files per page

function files_filesetdetail($args) {
	global $serverPath;
	
	//----------------------------------------------------------------------------------------------
	//	check args and authorisation
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }
	$authArgs = array('UID' => $args['refUID']);
	if (authHas($args['refModule'], 'files', $authArgs) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	load the file records and make html
	//----------------------------------------------------------------------------------------------
	$sql = "select * from files where refModule='" . sqlMarkup($args['refModule']) 
	     . "' and refUID='" . sqlMarkup($args['refUID']) . "'";
	
	$result = dbQuery($sql);	

	$html = '';
	$block = loadBlock('modules/files/listing.block.php');
	
	while($row = dbFetchAssoc($result)) {
		
		$f = new file();
		$f->loadArray(sqlRMArray($row));
		
		$labels = $f->extArray();
		
		$labels['editUrl'] = $serverPath . 'files/edit/return_uploadmultiple/' . $f->data['recordAlias'];
		if (authHas($row['refModule'], 'files', '') == false) {
		  $labels['editUrl'] = $serverPath . 'files/viewset/return_uploadmultiple/' 
		                     . $f->data['recordAlias'];
		}
		
		$returnUrl = '/files/uploadmultiple/refModule_' . $i->data['refModule'] 
			   . '/refUID_' . $f->data['refUID'] . '/';
		
		$labels['deleteForm'] = "
		<form name='deletefile' method='POST' action='/files/delete/' >
		<input type='hidden' name='action' value='deletefile' />
		<input type='hidden' name='UID' value='" . $f->data['UID'] . "' />
		<input type='hidden' name='return' value='" . $returnUrl . "' />
		<input type='submit' value='delete' />
		</form>
		";

		$html .= replaceLabels($labels, $block);
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>