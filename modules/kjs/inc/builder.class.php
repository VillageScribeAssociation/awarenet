<?php

//--------------------------------------------------------------------------------------------------
//*	KapentaJS Build Parser
//--------------------------------------------------------------------------------------------------

class KJS_Builder {

	var $buffered = false;							//_	set to true to buffer output [bool]
	var $output = '';								//_	optionally holds build javascript [string]
	var $makeFile = '';								//_	contents of makefile [string]

	//----------------------------------------------------------------------------------------------
	//	utility object for building KJS projects into a single file
	//----------------------------------------------------------------------------------------------
	//opt: makeFile - location of makefiule on disk [string]
	//opt: buffered - set to true to buffer output rather than printing directly to stdout [bool]

	function KJS_Builder($makeFile, $buffered = false) {
		global $kapenta;
	
		$this->buffered = $buffered;
		if (false == $buffered) { 	header('Content-type: text/javascript'); }

		if ('' !== $makeFile) {
			$this->makeFile = $kapenta->fileGetContents('data/kjs/build.txt');
			$this->process();
		}
	}

	//----------------------------------------------------------------------------------------------
	//	handles simple 'addfile' directives in makefile
	//----------------------------------------------------------------------------------------------
	//opt: js - javascript to add to build [string]

	function throwJs($js) {
		$js = $this->replaceTabs($js);

		//	special case to make JSLint less of a pain in the ass
		$js = str_replace("''", "String('')", $js);
		$js = str_replace("String(String(''))", "String('')", $js);
		$js = str_replace("\String('')", "\''", $js);

		$this->output .= $js . "\n";
		if (false == $this->buffered) {
			echo $this->output;
			$this->output = '';
		}
	}

	//----------------------------------------------------------------------------------------------
	//	process build script in $this->makeFile
	//----------------------------------------------------------------------------------------------

	function process() {
		$lines = explode("\n", $this->makeFile);
		foreach($lines as $line) {
			$line = trim($line);
			$add = true;
			if ('' == $line) { $add = false; }
			if ((true == $add) && ('#' == substr($line, 0, 1))) { $add = false; }

			$line = str_replace("\r", ' ', $line);
			$line = str_replace("\t", ' ', $line);
			$line = str_replace('  ', ' ', $line);
			$line = str_replace('  ', ' ', $line);
			$line = str_replace('  ', ' ', $line);
			$line = str_replace('  ', ' ', $line);
			$line = trim($line);

			if (true == $add) {

				$parts = explode(' ', $line);
				switch(strtolower($parts[0])) {			

					//------------------------------------------------------------------------------
					//	inline a single file
					//------------------------------------------------------------------------------
					case 'addfile':		$this->addFile($parts[1]);			break;

					//------------------------------------------------------------------------------
					//	register and add a whole KapentaJS module
					//------------------------------------------------------------------------------
					case 'addmodule':	$this->addModule($parts[1]);		break;

					//------------------------------------------------------------------------------
					//	register and add a theme
					//------------------------------------------------------------------------------
					//case 'addtheme':	$this->addTheme($parts[1]);			break;

					//------------------------------------------------------------------------------
					//	unknown directive
					//------------------------------------------------------------------------------
					default:
						$js .= "/* KapentJS import error, unrecognized: $line */\n";
						break;		//..............................................................

				}

			}

		}
	}

	//----------------------------------------------------------------------------------------------
	//	handles simple 'addfile' directives in makefile
	//----------------------------------------------------------------------------------------------

	function addFile($fileName) {
		global $kapenta;

		$js = '';
		if (true == $kapenta->fileExists($fileName)) {
			$js .= "/* KapentaJS import " . $fileName . " */\n\n";
			$js .= $kapenta->fileGetContents($fileName);
		} else {
			$js .= "/* ERROR: KapentaJS import, missing: " . $fileName . " */\n";
		}
		$this->throwJs($js);
	}

	//----------------------------------------------------------------------------------------------
	//	add an entire module
	//----------------------------------------------------------------------------------------------
	//arg: $moduleName - name of a kjs module [string]

	function addModule($moduleName) {
		global $kapenta;

		$baseDir = 'data/kjs/modules/' . $moduleName . '/';
		$js = '';

		if (true == $kapenta->fileExists($baseDir)) {

			$this->throwJs(''
			 . "/* KapentaJS adding module: " . $moduleName . " */\n\n"
			 . "kapenta.controllers.register('" . $moduleName . "');\n"
			);

			$files = $kapenta->fs->listDir($baseDir . 'models/', '.mod.js');
			foreach($files as $file) { $this->addModel($moduleName, $file); }
						
			$files = $kapenta->fs->listDir($baseDir . 'actions/', '.act.js');
			foreach($files as $file) { $this->addAction($moduleName, $file); }

			$files = $kapenta->fs->listDir($baseDir . 'actions/', '.page.js');
			foreach($files as $file) { $this->addPage($moduleName, $file); }

			$files = $kapenta->fs->listDir($baseDir . 'views/', '.fn.js');
			foreach($files as $file) { $this->addView($moduleName, $file); }

			$files = $kapenta->fs->listDir($baseDir . 'views/', '.block.js');
			foreach($files as $file) { $this->addBlock($moduleName, $file); }

		} else {
			$this->throwJs("/* KapentaJS module not found: " . $parts[1] . " */\n");
		}

	}

	//----------------------------------------------------------------------------------------------
	//.	add a model
	//----------------------------------------------------------------------------------------------
	//arg: moduleName - name of KJS module [string]
	//arg: fileName	 - name of a block file [string]

	function addModel($moduleName, $fileName) {
		global $kapenta;

		$obj = $kapenta->fs->get($fileName) . "\n";
		$modelName = str_replace('.mod.js', '', basename($fileName));

		$js = ''
		 . "/* Adding model: $fileName  */\n\n"
		 . "kapenta.models.add('" . $moduleName . "', '" . $modelName . "', " . trim($obj) . ");\n";

		$this->throwJs($js);
	}

	//----------------------------------------------------------------------------------------------
	//.	add an action
	//----------------------------------------------------------------------------------------------
	//arg: moduleName - name of KJS module [string]
	//arg: fileName	 - name of a block file [string]

	function addAction($moduleName, $fileName) {
		global $kapenta;

		$raw = $kapenta->fs->get($fileName) . "\n";
		$indented = '';
		$actionName = str_replace('.act.js', '', basename($fileName));

		$lines = explode("\n", $raw);
		foreach($lines as $line) {
			$indented .= "\t" . $line . "\n";
		}

		$js = ''
		 . "/* Adding action: $fileName  */\n\n"
		 . "kapenta.actions.add('" . $moduleName . "', '" . $actionName . "', function(ac) {\n\n"
		 . $indented
		 . "});\n";

		$this->throwJs($js);
	}

	//----------------------------------------------------------------------------------------------
	//.	add a page
	//----------------------------------------------------------------------------------------------
	//arg: moduleName - name of KJS module [string]
	//arg: fileName	 - name of a block file [string]

	function addPage($moduleName, $fileName) {
		global $kapenta;

		$rawBlock = $kapenta->fs->get($fileName) . "\n";
		$pageName = str_replace('.page.js', '', basename($fileName));
		$title = '';
		$menu = '';
		$content = '';
		$js = '';

		$lines = explode("\n", $rawBlock);

		foreach($lines as $line) {
			if ('#comment:' === substr($line, 0, 9)) { $line = ''; }
			if ('#title:' === substr($line, 0, 7)) { $title = trim(substr($line, 7)); $line = ''; }
			if ('#menu:' === substr($line, 0, 6)) { $menu = trim(substr($line, 6)); $line = ''; }

			if ('' !== trim($line)) {
				$content .= "\t+ '" . str_replace("'", "\\'", $line) . "'\n";
			}

		}

		$js .= ''
		 . "/* Adding page template: $fileName  */\n\n"
		 . "kapenta.pages.add('" . $moduleName . "', '" . $pageName . "', {\n"
		 . "\t'title': '" . $title . "',\n"
		 . "\t'menu': '" . $menu . "',\n"
		 . "\t'content': ''\n"
		 . $content
		 . "});\n";

		$this->throwJs($js);
	}

	//----------------------------------------------------------------------------------------------
	//.	add a view
	//----------------------------------------------------------------------------------------------
	//arg: moduleName - name of KJS module [string]
	//arg: fileName	 - name of a block file [string]

	function addView($moduleName, $fileName) {
		global $kapenta;

		$raw = $kapenta->fs->get($fileName) . "\n";
		$indented = '';
		$viewName = str_replace('.fn.js', '', basename($fileName));

		$lines = explode("\n", $raw);
		foreach($lines as $line) {
			$indented .= "\t" . $line . "\n";
		}

		$js = ''
		 . "/* Adding view: $fileName  */\n\n"
		 . "kapenta.views.add('" . $moduleName . "', '" . $viewName . "', function(ac) {\n\n"
		 . $indented
		 . "});\n";

		$this->throwJs($js);
	}


	//----------------------------------------------------------------------------------------------
	//.	add blocks / view template
	//----------------------------------------------------------------------------------------------
	//;	note that blocks must be escaped and wrapped in a function to register them
	//arg: moduleName - name of KJS module [string]
	//arg: fileName	 - name of a block file [string]

	function addBlock($moduleName, $fileName) {
		global $kapenta;

		if (false == $kapenta->fileExists($fileName)) {
			$this->throwJs("/*  ERROR: missing block file $fileName */");
			return;
		}

		$viewName = str_replace('.block.js', '', basename($fileName));
		$rawBlock = $kapenta->fs->get($fileName) . "\n";
		$cleanBlock = '';
		$js = '';

		$lines = explode("\n", $rawBlock);

		foreach($lines as $line) {
			if ('' != trim($line)) {
				$cleanBlock .= "\t+ '" . str_replace("'", '\\' . "'", $line) .  "'\n";
			}
		}

		$js .= ''
		 . "/* Adding view template / block: $fileName  */\n\n"
		 . "kapenta.blocks.add('" . $moduleName . "', '" . $viewName . "', ''\n"
		 . $cleanBlock
		 . ");\n";

		$this->throwJs($js);
	}

	//----------------------------------------------------------------------------------------------
	//	utility function to replace tabs with spaces
	//----------------------------------------------------------------------------------------------

	function replaceTabs($txt) {
		$clean = '';
		$len = strlen($txt);
		$chr = '';	
		$cur = 0;

		for ($i = 0; $i < $len; $i++) {
			$chr = substr($txt, $i, 1);

			if ("\n" === $chr) { $cur = 0; }

			if ("\t" === $chr) {
				switch (($cur - 1) % 4) {
					case 0:	$cur += 4;	$chr = '    ';	break;
					case 1:	$cur += 3;	$chr = '   ';	break;
					case 2:	$cur += 2;	$chr = '  ';	break;
					case 3:	$cur += 1;	$chr = ' ';		break;
				}

			} else {
				$cur++;
			}

			$clean .= $chr;
		}

		return $clean;
	}

}

?>
