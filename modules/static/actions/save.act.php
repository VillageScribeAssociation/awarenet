<?

//--------------------------------------------------------------------------------------------------------------
//	save submitted changes, redirect to /static/Saved-Page
//--------------------------------------------------------------------------------------------------------------

	if (authHas('static', 'save', '') == false) { do403(); }
	require_once($installPath . 'modules/static/models/static.mod.php');

	if ( (array_key_exists('action', $_POST) 
		AND ($_POST['action'] == 'saveStaticPage'))
		AND (array_key_exists('UID', $_POST)) 
		AND (dbRecordExists('static', $_POST['UID']))
	    ) {
		
		$model = new StaticPage(sqlMarkup($_POST['UID']));
		$fields = explode('|', 'title|content|menu1|menu2|nav1|nav2|script|head');
		foreach($fields as $field) {
		  if ((array_key_exists($field, $_POST)) && (array_key_exists($field, $model->data))) {
			$model->data[$field] = $_POST[$field];
		  }
		}
		
		$model->save();
		do302('static/' . $model->data['recordAlias']);
		
	} else { do404(); }

?>