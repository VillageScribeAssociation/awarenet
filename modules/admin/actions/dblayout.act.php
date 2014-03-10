<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');

//-------------------------------------------------------------------------------------------------
//*	display all database schema
//-------------------------------------------------------------------------------------------------

	$html = '';

	//---------------------------------------------------------------------------------------------
	//	only admins may do this
	//---------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//---------------------------------------------------------------------------------------------
	//	list tables, update and print them
	//---------------------------------------------------------------------------------------------

	$dba = new KDBAdminDriver();

	$tables = $kapenta->db->loadTables();
	foreach($tables as $tableName) {
		$dbSchema = $kapenta->db->getSchema($tableName);
		$html .= $dba->schemaToHtml($dbSchema) . "<hr/>\n";
	}

	//---------------------------------------------------------------------------------------------
	//	render the page
	//---------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/admin/actions/dblayout.page.php');
	$kapenta->page->blockArgs['tablesdisplay'] = $html;
	//$page->data['template'] = str_replace('%%tablesdisplay%%', $html, $page->data['template']);
	$kapenta->page->render();

?>
