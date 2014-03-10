<?

//--------------------------------------------------------------------------------------------------
//*	list all aliases (globally, by module, by model or by object)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }
	
	//----------------------------------------------------------------------------------------------
	//	check for any arguments
	//----------------------------------------------------------------------------------------------
	$fModule = '*';
	$fModel = '*';
	$fUID = '*';
	$aliasTitle = "All Aliases";

	if (true == array_key_exists('fmodule', $kapenta->request->args)) { 
		if (true == $kapenta->moduleExists($kapenta->request->args['fmodule'])) { $fModule = $kapenta->request->args['fmodule']; }
	}

	if (true == array_key_exists('fmodel', $kapenta->request->args)) {
		if (true == $kapenta->db->tableExists($kapenta->request->args['fmodel'])) { $fModel = $kapenta->request->args['fmodule']; }
	}

	if (('*' == $fModule) && ('*' == $fModel) && ('*' == $fUID)) { 
		$aliasTitle = 'All Aliases'; 
	} else {
		if (('*' == $fModel) && ('*' == $fUID)) {	
			$aliasTitle = 'All Aliases in Module ' . $fModule; 
		} else {
			if ('*' == $fUID) {	
				$aliasTitle = 'All Aliases of ' . $fModel . ' objects.'; 
			} else {
				$aliasTitle = 'Aliases of object ' . $fUID . ' (' . $fModel . ').'; 
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/aliases/actions/list.page.php');
	$kapenta->page->blockArgs['filterModule'] = $fModule;
	$kapenta->page->blockArgs['filterModel'] = $fModel;
	$kapenta->page->blockArgs['filterUID'] = $fUID;
	$kapenta->page->blockArgs['aliasTitle'] = $aliasTitle;
	$kapenta->page->render();	

?>
