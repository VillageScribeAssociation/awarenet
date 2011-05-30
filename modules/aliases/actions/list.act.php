<?

//--------------------------------------------------------------------------------------------------
//*	list all aliases (globally, by module, by model or by object)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	
	//----------------------------------------------------------------------------------------------
	//	check for any arguments
	//----------------------------------------------------------------------------------------------
	$fModule = '*';
	$fModel = '*';
	$fUID = '*';
	$aliasTitle = "All Aliases";

	if (true == array_key_exists('fmodule', $req->args)) { 
		if (true == $kapenta->moduleExists($req->args['fmodule'])) { $fModule = $req->args['fmodule']; }
	}

	if (true == array_key_exists('fmodel', $req->args)) {
		if (true == $db->tableExists($req->args['fmodel'])) { $fModel = $req->args['fmodule']; }
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
	$page->load('modules/aliases/actions/list.page.php');
	$page->blockArgs['filterModule'] = $fModule;
	$page->blockArgs['filterModel'] = $fModel;
	$page->blockArgs['filterUID'] = $fUID;
	$page->blockArgs['aliasTitle'] = $aliasTitle;
	$page->render();	

?>
