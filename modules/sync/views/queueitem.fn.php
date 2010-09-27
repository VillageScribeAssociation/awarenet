<?

	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');
	require_once($kapenta->installPath . 'modules/sync/models/notice.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary list 
//--------------------------------------------------------------------------------------------------
//arg: itemUID - UID of sync queue item [string]

function sync_queueitem($args) {
	global $theme, $user;
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (false == array_key_exists('itemUID', $args)) { return ''; }

	$model = new Sync_Notice($args['itemUID']);
	if (false == $model->loaded) { return '' }
	$ext = $model->extArray();

	//---------------------------------------------------------------------------------------------
	//	conditional - dbUpdate
	//---------------------------------------------------------------------------------------------
	$cond = '';
	
	switch ($model->type) {
		
		case "dbUpdate":
				//---------------------------------------------------------------------------------
				//	 a database (SQL) update, base64 encoded XML
				//---------------------------------------------------------------------------------
				$cond .= "<h2>Database (SQL) update, base64 encoded XML</h2>\n";

				$doc = new KXmlDocument($model->data);

				$kids = $doc->getChildren();		//% handles to children of root node [array]
				foreach($kids as $childId) {
					$child = $doc->getEntity($childId);
					if ('fields' == $child['type']) {
						$fields = $doc->getChildren2d($childId);
						$fTable = array();
						$fTable[] = array('Field', 'Value');

						foreach($fields as $fName => $fVal) 
							{ $fTable[] = array($fName, base64_decode($fVal)); }

						$cond .= $theme->arrayToHtmlTable($fTable, true, true);
					}
				}

				/*
				$xe = new XmlEntity($model->data);
				foreach ($xe->children as $child) {
					if ('fields' == $child->type) {
						$fTable = array();
						$fTable[] = array('Field', 'Value');
						foreach ($child->children as $field) { 
							$fTable[] = array($field->type, base64_decode($field->value)); 
						}
						$cond .= $theme->arrayToHtmlTable($fTable, true, true);
					}
				}
				*/

				break;	//	........................................................................

		case "dbDelete":
				//---------------------------------------------------------------------------------
				//	 a database (SQL) update, base64 encoded XML
				//---------------------------------------------------------------------------------
				$cond .= "<h2>Database (SQL) deletion instruction, XML</h2>\n";

				break;	//	........................................................................

	}


	$ext['conditionalData'] = $cond;
	$html = $theme->replaceLabels($ext, $theme->loadBlock('modules/sync/views/queueitem.block.php'));
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
