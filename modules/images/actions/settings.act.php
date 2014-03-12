<?

//--------------------------------------------------------------------------------------------------
//*	image module settings
//--------------------------------------------------------------------------------------------------
//+	Allowed image dimensions are stored in registry keys as either fixed size or fixed width.

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	define default file associations
	//----------------------------------------------------------------------------------------------

	$assoc = array('jpg', 'jpeg', 'png', 'gif');

	//----------------------------------------------------------------------------------------------
	//	check default preset sizes exist
	//----------------------------------------------------------------------------------------------
	$defaults = array(
		'full' => '*x*',
		'thumb' => '100x100',
		'thumbsm' => '50x50',
		'thumb90' => '90x90',
		'width100' => '100x*',
		'width145' => '145x*',
		'width190' => '190x*',
		'width200' => '200x*',
		'width290' => '290x*',
		'width300' => '300x*',
		'width320' => '300x*',
		'width560' => '560x*',
		'width570' => '570x*',
		'widtheditor' => '530x*',
		'widthcontent' => '570x*',
		'widthindent' => '515x*',
		'widthmax' => '1024x*',
		'widthnav' => '296x*',
		'slide' => '560x300',
		'slideindent' => '520x300',
		'mobile' => '320x*',
		'mobileslide' => '320x180',
		'content' => '570x*',
		'indent' => '500x*',
		'nav1' => '300x*',
		'nav2' => '300x*'
	);

	foreach($defaults as $label => $value) {
		$key = 'images.size.' . $label;
		if ('' == $kapenta->registry->get($key)) { $kapenta->registry->set($key, $value);	}
	}

	//----------------------------------------------------------------------------------------------
	//	reset image sizes
	//----------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('loadDefaults' == $_POST['action'])) {
		foreach($defaults as $label => $value) {
			$key = 'images.size.' . $label;
			$kapenta->registry->set($key, $value);
		}
	}

	//----------------------------------------------------------------------------------------------
	//	add a preset image size
	//----------------------------------------------------------------------------------------------
	if ((true == array_key_exists('action', $_POST)) && ('addPreset' == $_POST['action'])) {
		if (false == array_key_exists('label', $_POST)) { $kapenta->page->do404('Label not given'); }
		if (false == array_key_exists('width', $_POST)) { $kapenta->page->do404('Width not given'); }
		if (false == array_key_exists('height', $_POST)) { $kapenta->page->do404('Height not given'); }
		if (false == array_key_exists('watermark', $_POST)) { $kapenta->page->do404('WM not given'); }
		
		$label = trim(strtolower($_POST['label']));		//TODO: better sanitzation
		$width = (int)$_POST['width'];
		$height = (int)$_POST['height'];
		$watermark = '';
		if ('*' == $_POST['width']) { $width = '*'; }
		if ('*' == $_POST['height']) { $height = '*'; }
		if ('yes' == $_POST['watermark']) { $watermark = 'w'; }

		$kapenta->registry->set('images.size.' . $label, $width . 'x' . $height . $watermark);
	}

	//----------------------------------------------------------------------------------------------
	//	delete a preset
	//----------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('deletePreset' == $_POST['action'])) {
		if (false == array_key_exists('label', $_POST)) { $kapenta->page->do404('Label not given'); }	
		$label = trim(strtolower($_POST['label']));		//TODO: better sanitzation
		$kapenta->registry->delete('images.size.' . $label);
	}

	//----------------------------------------------------------------------------------------------
	//	set default file associations with this module
	//----------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('resetFileAssoc' == $_POST['action'])) {
		$reg = $kapenta->registry->search('live', 'live.file.');

		//	delete existing file associations with this module
		foreach($reg as $key => $value) {
			if ('images' == $value) { $kapenta->registry->delete($key); }
		}

		//	recreate defaults
		foreach($assoc as $ext) { $kapenta->registry->set('live.file.' . $ext, 'images'); }
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/images/actions/settings.page.php');
	$kapenta->page->render();

?>
