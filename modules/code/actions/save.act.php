<?

//--------------------------------------------------------------------------------------------------
//	save a code entry
//--------------------------------------------------------------------------------------------------

	if (authHas('code', 'edit', '') == false) { $kapenta->page->do403(); }
	require_once($kapenta->installPath . 'modules/code/models/code.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/coderevision.mod.php');

	if (array_key_exists('action', $_POST)) {

		//------------------------------------------------------------------------------------------
		//	save from 'add item' form on folder
		//------------------------------------------------------------------------------------------

		if ($_POST['action'] ==  'addDocument') {

			$parent = new Code($_POST['UID']);

			$c = new Code();
			$c->data['project'] = $parent->data['project'];
			$c->data['parent'] = $_POST['UID'];
			$c->data['type'] = $_POST['type'];
			$c->data['title'] = $_POST['title'];
			$c->data['version'] = $parent->data['version'];
			$c->data['revision'] = 0;
			$c->data['description'] = $_POST['description'];
			$c->data['content'] = $_POST['content'];
			$c->data['author'] = $kapenta->user->UID;
			$c->save();
			$kapenta->page->do302('code/' . $c->data['recordAlias']);

		}

		//------------------------------------------------------------------------------------------
		//	save from edit form
		//------------------------------------------------------------------------------------------
		if ($_POST['action'] == 'saveCodeRecord') {
			if ($kapenta->db->objectExists('code', $_POST['UID']) == false) { $kapenta->page->do404(); }
			$c = new Code($_POST['UID']);

			//--------------------------------------------------------------------------------------
			//	make a note of revision
			//--------------------------------------------------------------------------------------
			$revision = new CodeRevision();
			$revision->data['UID'] = $kapenta->createUID();
			$revision->data['refUID'] = $_POST['UID'];
			$revision->data['type'] = $c->data['type'];
			$revision->data['title'] = $c->data['title'];
			$revision->data['version'] = $c->data['version'];
			$revision->data['revision'] = $c->data['revision'];
			$revision->data['description'] = $c->data['description'];
			$revision->data['content'] = $c->data['content'];
			$revision->data['editedBy'] = $kapenta->user->UID;
			$revision->data['editedOn'] = mysql_datetime();
			$revision->data['reason'] = $_POST['reason'];
			$revision->save();

			//--------------------------------------------------------------------------------------
			//	save the record
			//--------------------------------------------------------------------------------------
			$c->data['title'] = $_POST['title'];
			$c->data['description'] = $_POST['description'];
			$c->data['content'] = $_POST['content'];
			$c->data['type'] = $_POST['type'];
			$c->data['revision'] = ($c->data['revision'] + 1);
			$c->save();
			$kapenta->session->msg("Updated code record.<br/>\n");
			$kapenta->page->do302('code/' . $c->data['recordAlias']);
			
		}

		//----------------------------------------------------------------------------------------------
		//	create subfolders
		//----------------------------------------------------------------------------------------------
		if ($_POST['action'] == 'addSubFolder') {

			$parent = new Code($_POST['UID']);

			$c = new Code();
			$c->data['project'] = $parent->data['project'];
			$c->data['parent'] = $_POST['UID'];
			$c->data['type'] = 'folder';
			$c->data['title'] = $_POST['folder'];
			$c->data['version'] = $parent->data['version'];
			$c->data['revision'] = 0;
			$c->data['description'] = '';
			$c->data['content'] = '';
			$c->data['author'] = $kapenta->user->UID;
			$c->save();
			$kapenta->page->do302('code/' . $c->data['UID']);

		}
	}

?>
