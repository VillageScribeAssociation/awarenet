<?

//--------------------------------------------------------------------------------------------------
//	object for reading, writing and rendering pages
//--------------------------------------------------------------------------------------------------

class Page {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $fileName;		// or the .page.php file being loaded
	var $UID;
	var $data;
	var $errMsg = '';
	var $style;
	var $blockArgs;

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function Page($fileName = '') {
		global $defaultTheme;
		$this->UID = createUID();										// current render ID
		$this->style = themeReadStyle($defaultTheme);					// style variables
		$this->blockArgs = array();
		$this->data = array(
			'template' => '', 'content' => '', 'title' => '', 
			'script' => '', 'nav1' => '','nav2' => '', 'banner' => '', 
			'head' => '', 'menu1' => '', 'menu2' => '', 'section' => '', 
			'subsection' => '', 'breadcrumb' => '', 'jsinit' => '');	// the parts of a page

		if ($fileName != '') { $this->load($fileName); }
	}

	//----------------------------------------------------------------------------------------------
	//	load page template from a file
	//----------------------------------------------------------------------------------------------

	function load($fileName) {
		$this->fileName = $fileName;
		$xe = xmlLoad($fileName);
		if (false == $xe) { 
			logErr('pages', 'load', 'could not load: ' . $fileName);
			return false; 
		}

		foreach($this->data as $key => $val) {
			$tag = $xe->getFirst($key);
			$this->data[$key] = sqlRemoveMarkup($tag); 
		} 

	}

	//----------------------------------------------------------------------------------------------
	//	save to file
	//----------------------------------------------------------------------------------------------

	function save() {
		$xml = '';
		foreach($this->data as $k => $v) 
			{ $xml .= "\t<" . $k . '>' . sqlMarkup(stripslashes($v)) . '</' . $k . ">\n"; }

		$xml = "<? /" . "*\n<page>\n" . $xml . "</page>\n*" . "/ ?>";
		$fH = fopen($this->fileName, 'w+');
		fwrite($fH, $xml);
		fclose($fH);
	}

	//----------------------------------------------------------------------------------------------
	//	render (all blocks in all template areas)
	//----------------------------------------------------------------------------------------------

	function render() {
		global $installPath;
		global $serverPath;
		global $defaultTheme;
		global $websiteName;
		global $request;
		global $user;

		//------------------------------------------------------------------------------------------
		//	set some universals
		//------------------------------------------------------------------------------------------

		$d = $this->data;
		$d['websiteName'] = $websiteName;
		$d['serverPath'] = $serverPath;
		$d['moduleName'] = $request['module'];
		$d['defaultTheme'] = $defaultTheme;
		$d['sMessage'] = $_SESSION['sMessage'];
		$d['pageInstanceUID'] = $this->UID;
		$d['jsUserUID'] = $user->data['UID'];

		$_SESSION['sMessage'] = ''; // send messag to user once only

		//------------------------------------------------------------------------------------------
		//	load the template
		//------------------------------------------------------------------------------------------
		$template = implode(file($installPath . 'themes/' . $defaultTheme . '/' . $d['template']));
		$template = phpUnComment($template);

		//------------------------------------------------------------------------------------------
		//	decide on a menu
		//------------------------------------------------------------------------------------------

		$menuFile = 'themes/clockface/usermenu.block.php';
		if ($user->data['ofGroup'] == 'public') { $menuFile = 'themes/clockface/publicmenu.block.php'; }
		$d['menu1'] = loadBlock($menuFile);
		
		//------------------------------------------------------------------------------------------
		//	perform the replacements
		//------------------------------------------------------------------------------------------
		foreach($d as $find => $replace) {
		  if ($find != 'template') {
			$template = str_replace('%%' . $find . '%%', $replace, $template);
		  }	
		}

		foreach($this->blockArgs as $blockVar => $blockVal) {
			$template = str_replace('%%' . $blockVar . '%%', $blockVal, $template);
		}

		//------------------------------------------------------------------------------------------
		//	execute blocks
		//------------------------------------------------------------------------------------------
		$template = expandBlocks($template, '');

		//------------------------------------------------------------------------------------------
		//	style 
		//------------------------------------------------------------------------------------------
		foreach($this->style as $find => $replace) {
			$template = str_replace('%%' . trim($find) . '%%', $replace, $template);
		}

		//------------------------------------------------------------------------------------------
		//	special admin option
		//------------------------------------------------------------------------------------------
		if ($user->data['ofGroup'] == 'admin') {
			//$fileName = str_replace($installPath, '', $this->fileName);
			//$parts = explode('/', $fileName);
			//$editLink = "<a href='/pages/edit/module_" . $parts[1] . "/" . $parts[2] 
			//	  . "'>[edit this page]</a>";

			//$template = str_replace('</body>', $editLink . "\n</body>", $template);
		}

		//------------------------------------------------------------------------------------------
		//	perform replacements again after blocks
		//------------------------------------------------------------------------------------------
		foreach($d as $find => $replace) {
		  if ($find != 'template') {
			$template = str_replace('%%' . $find . '%%', $replace, $template);
		  }	
		}

		foreach($this->blockArgs as $blockVar => $blockVal) {
			$template = str_replace('%%' . $blockVar . '%%', $blockVal, $template);
		}

		$template = str_replace('%%' . 'delme%%', '', $template);

		//------------------------------------------------------------------------------------------
		//	convert relative URLs to absolute URLs
		//------------------------------------------------------------------------------------------

		$replaceset = explode('|', 'href|src|background|action');
		foreach($replaceset as $toAbs) {
			$template = str_replace($toAbs . "='/", $toAbs . "='" . $serverPath, $template);
			$template = str_replace($toAbs . "=/", $toAbs . "=" . $serverPath, $template);
			$template = str_replace($toAbs . "=\"/", $toAbs . "=\"" . $serverPath, $template);
		}

		//------------------------------------------------------------------------------------------
		//	log the page view and send the page
		//------------------------------------------------------------------------------------------
		logPageView();
		echo $template;

	}

	//----------------------------------------------------------------------------------------------
	//	copy arguments from $request to $this->blockArgs ($argNames is a comma separated list)
	//----------------------------------------------------------------------------------------------

	function allowBlockArgs($argNames) {
		global $request;
		$argNames = explode(',', $argNames);  
		foreach($argNames as $argName) {
			$argName = trim($argName);
			if (array_key_exists($argName, $request['args'])) {
				$this->blockArgs[$argName] = $request['args'][$argName];
			}
		}
	}

}

?>
