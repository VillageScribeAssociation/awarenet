<?

//--------------------------------------------------------------------------------------------------
//	create a new project
//--------------------------------------------------------------------------------------------------

	if (authHas('code', 'edit', '') == false) { $kapenta->page->do403(); }

	if (array_key_exists('project', $_POST)) { 

		require_once($kapenta->installPath . 'modules/code/models/code.mod.php');
		$c = new Code();
		$c->data['UID'] = $kapenta->createUID();
		$c->data['type'] = 'folder';
		$c->data['parent'] = 'none';
		$c->data['title'] = '/';

		$c->data['project'] = $_POST['project'];

		$c->save();
	
		$kapenta->page->do302('code/edit/' . $c->data['UID']);

	}

?>
