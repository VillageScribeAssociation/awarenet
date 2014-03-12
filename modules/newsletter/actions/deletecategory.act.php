<?php

	require_once($kapenta->installPath . 'modules/newsletter/models/category.mod.php');
	require_once($kapenta->installPath . 'modules/newsletter/models/edition.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a category
//--------------------------------------------------------------------------------------------------
//postarg: UID - UID of a Newsletter_Category object to delete [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------

	if ('admin' != $kapenta->user->role) { $kapenta->page->do403("Not authorized", true); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('UID not given', true); }

	$model = new Newsletter_Category($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Category not found', true); }

	//----------------------------------------------------------------------------------------------
	//	check that no notices are currently in this category
	//----------------------------------------------------------------------------------------------

	$conditions = array("category='" . $kapenta->db->addMarkup($model->UID) . "'");
	$range = $kapenta->db->loadRange('newsletter_notice', '*', $conditions);

	if (0 != count($range)) {

		echo ''
		 . "<h2>Cannot delete category: " . $model->name . "</h2>\n"
		 . "<p>The following editions still have notices in this category, they must be deleted or "
		 . "moved to another category first.</p>\n";

		foreach($range as $item) {
			$edition = new Newsletter_Edition($item['edition']);

			echo $theme->expandBlocks("[[:theme::ifscrollheader:]]");

			echo ''
			 . "<a "
			 . "href='" . $kapenta->serverPath . "newsletter/showedition/" . $edition->alias . "' "
			 . "target='_parent'"
			 . ">" . $edition->subject . "</a><br/>\n";

			echo $theme->expandBlocks("[[:theme::ifscrollfooter:]]");

		}

	} else {

		//------------------------------------------------------------------------------------------
		//	delete it and close the window
		//------------------------------------------------------------------------------------------
	
		$model->delete();

		echo ''
		 . "<script>\n"
		 . "	var UID = window.name.replace('ifc', '');\n"
		 . "	if ((window.parent) && (window.parent.kwindowmanager)) {\n"
		 . "		var kwm = window.parent.kwindowmanager;\n"
		 . "		var hWnd = kwm.getIndex(UID);\n"
		 . "		window.parent.newsletter_reloadsubscriptions();\n"
		 . "		window.parent.kwindowmanager.closeWindow(UID);\n"
		 . "	}\n"
		 . "</script>\n";

	}

?>
