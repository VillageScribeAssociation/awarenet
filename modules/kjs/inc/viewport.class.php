<?php

	require_once($kapenta->installPath . 'modules/kjs/inc/parser.inc.php');

//--------------------------------------------------------------------------------------------------
//*	utility object to help port kapenta views to Kapenta.JS
//--------------------------------------------------------------------------------------------------

class KJS_ViewPort {

	var $module = '';			//_	name of Kapenta.JS module [string]
	var $base = '';				//_	name of kapenta module [string]

	var $fnFiles = array();		//_	set of .fn.php file names [array:string]
	var $blockFiles = array();	//_	set of .block.php file names [array:string]

	var $report = '';			//_	HTML report of module conversion [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//arg: moduleName - name of new Kapenta.JS module [string]
	//arg: baseName - name of existing kapenta module [string]

	function KJS_ViewPort($moduleName, $baseName) {
		global $kapenta;

		$this->module = $moduleName;
		$this->base = $baseName;

		$this->fnFiles = $kapenta->fileSearch('modules/' . $baseName . '/views/', '.fn.php');
		$this->blockFiles = $kapenta->fileSearch('modules/' . $baseName . '/views/', '.block.php');

		$this->report .= "<h2>Porting views</h2>";
		foreach($this->fnFiles as $fileName) { $this->report .= "Extant: $fileName<br/>"; }
		foreach($this->blockFiles as $fileName) { $this->report .= "Extant: $fileName<br/>"; }

	}

	//----------------------------------------------------------------------------------------------
	//.	convert and copy all block templates
	//----------------------------------------------------------------------------------------------

	function copyBlocks() {
		global $theme;
		global $kapenta;
		global $kapenta;

		foreach($this->blockFiles as $fileName) {
			$newFile = ''
			 . "data/kjs/modules/" . $this->module . "/views/"
			 . str_replace(".block.php", ".block.js", basename($fileName));

			$raw = $theme->loadBlock($fileName);
			$lines = explode("\n", $raw);

			$js = ''
			 . "//" . str_repeat('-', 80) . "\n"
			 . "//*\tported from " . $fileName . "\n"
			 . "//" . str_repeat('-', 80) . "\n"
			 . "//:\tCreated: " . $kapenta->datetime() . " By: " . $kapenta->user->getName() . "\n"
			 . "\n"
			 . "kapenta.modules.addBlock(\n"
			 . "\t'" . $this->module . "',\n"
			 . "\t'" . str_replace('.block.php', '', basename($fileName)) . "',\n"
			 . "\t''\n";

			foreach($lines as $line) {
				$line = str_replace("\"", "\\\"", $line);
				$line = str_replace("\n", '', $line);
				$line = str_replace("\r", '', $line);
				$js .= "\t + \"" . $line . "\"\n";
			}

			$js .= ");\n";

			$kapenta->fs->put($newFile, $js);

			$this->report .= "<b>Creting: $newFile</b><br/>\n";
			$this->report .= "<small><pre>\n" . htmlentities($js) . "\n</pre></small><br/>\n";
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	convert and copy all views
	//----------------------------------------------------------------------------------------------

	function copyViews() {
		global $theme;
		global $kapenta;
		global $kapenta;

		foreach($this->fnFiles as $fileName) {
			$newFile = ''
			 . "data/kjs/modules/" . $this->module . "/views/"
			 . str_replace(".fn.php", ".fn.js", basename($fileName));

			$raw = $theme->loadBlock($fileName);

			//--------------------------------------------------------------------------------------
			//	convert to Javascript and extract function body and doc comments
			//--------------------------------------------------------------------------------------

			$tokens = kjs_tokenize($raw);	//%	[array:array:string]
			$tokens = kjs_rough_convert_js($tokens);

			$cbCount = 0;					//%	number of curly braces
			$openComment = '';				//%	doc comments

			foreach($tokens as $idx => $tk) {
				switch($tk['type']) {
					case '{':
						if (0 == $cbCount) {
							//	variable definitions get tasked on here
							$tokens[$idx]['value'] = substr($tokens[$idx]['value'], 1);
						}
						$cbCount++;
						break;	//..................................................................

					case '}':
						$cbCount--;
						break;	//..................................................................

					case 'T_COMMENT':

						switch(substr($tk['value'], 0, 3)) {
							case '//*':	$openComment .= $tk['value'] . "\n";		break;
							case '//|':	$openComment .= $tk['value'] . "\n";		break;
							case '//a':	$openComment .= $tk['value'] . "\n";		break;
							case '//o':	$openComment .= $tk['value'] . "\n";		break;
							case '//:':	$openComment .= $tk['value'] . "\n";		break;
							case '//;':	$openComment .= $tk['value'] . "\n";		break;

							case "//-":
								if (strlen($tk['value']) > 96) {
									$openComment .= $tk['value'] . "\n";
								}
								break;
						}

						break;
				}
				if (0 == $cbCount) { $tokens[$idx]['value'] = ''; }
			}

			$js = ''
			 . $openComment
			 . "//:\tported from " . $fileName . "\n"
			 . "//:\tCreated: " . $kapenta->datetime() . " By: " . $kapenta->user->getName() . "\n"
			 . "\n"
			 . "kapenta.modules.addView(\n"
			 . "  '" . $this->module . "',\n"
			 . "  '" . str_replace('.fn.php', '', basename($fileName)) . "',\n"
			 . "  function(args) {\n";

			foreach($tokens as $token) { $js .= $token['value']; }

			$js .= ''
			 . "  }\n"
			 . ");\n";

			//--------------------------------------------------------------------------------------
			//	brute convert args to object
			//--------------------------------------------------------------------------------------

			$newJs = '';
			$lines = explode("\n", $js);
			$last = '';
			foreach($lines as $line) {
				$startPos = strpos($line, "kapenta.php.array_key_exists");
				$endPos = strpos($line, ", args)");
				if ((false !== $startPos) && (false !== $endPos) && ($endPos > $startPos)) {
					$line = ''
					 . substr($line, 0, $startPos)
					 . substr($line, $startPos + 28, $endPos - ($startPos + 28))
					 . " in " . substr($line, $endPos + 2);
				}

				if (('' == $last) && ('' == trim($line))) {
					// don't add two empty lines in a row
				} else {
					$newJs .= $line . "\n";
				}

				$last = trim($line);
			}
			$js = $newJs;


			//--------------------------------------------------------------------------------------
			//	save to disk
			//--------------------------------------------------------------------------------------

			$kapenta->fs->put($newFile, $js);

			$this->report .= "<b>Creting: $newFile</b><br/>\n";
			$this->report .= "<small><pre>\n" . htmlentities($js) . "\n</pre></small><br/>\n";
		}
	}

}

?>
