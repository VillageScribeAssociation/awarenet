<?

//--------------------------------------------------------------------------------------------------------------
//	save submitted changes, redirect to /static/Saved-Page
//--------------------------------------------------------------------------------------------------------------

	if ($user->authHas('home', 'Home_Static', 'save', 'TODO:UIDHERE') == false) { $page->do403(); }
	require_once($kapenta->installPath . 'modules/static/models/static.mod.php');

	if ( (array_key_exists('action', $_POST) 
		AND ($_POST['action'] == 'saveStaticPage'))
		AND (array_key_exists('UID', $_POST)) 
		AND ($kapenta->db->objectExists('static', $_POST['UID']))
	    ) {
		
		$model = new StaticPage($kapenta->db->addMarkup($_POST['UID']));
		$fields = explode('|', 'title|content|menu1|menu2|nav1|nav2|script|head');
		foreach($fields as $field) {
		  if ((array_key_exists($field, $_POST)) && (array_key_exists($field, $model->data))) {
			$model->data[$field] = $_POST[$field];
		  }
		}
		
		$model->save();
		$page->do302('static/' . $model->alias);
		
	} else { $page->do404(); }

?>