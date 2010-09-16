<?

//--------------------------------------------------------------------------------------------------
//*	functions for loading, executing and displaying blocks (transcluded sections)
//--------------------------------------------------------------------------------------------------

//+	Modules, themes and the kapenta core express a blocks API for dynamicaly transcluding data as a 
//+	page is requested.  Generally, evey section on a page will be expressed as a block.  They have 
//+	the following format:
//+
//+	[[::blockapi::method::argument::]]
//+
//+	For example:
//+
//+	[[::blog::show::UID=23498723484::]]		// generate HTML of blog post UID:23498723484
//+	[[::blog::navlist::num=10::start=0::]]	// list the 10 latest blog posts, formatted for nav bar
//+
//+	BlockAPI may be a module, 'theme' for the current theme's api (menus, pagination, etc) and the 
//+	method is a hook defined on that API, with any number of arguments.  Blocks may return HTML or 
//+	text including other blocks which are processed recursively.  One must be careful when 
//+	designing pages not to allow an infinite loop to occur: [[block A]] loads [[block B]] which 
//+	loads [[block A]] again
//+
//+	Access control is provided by the block API itself consulting the users permissions.  Blocks 
//+	may also be used in determining a users level of access by returning data to the permissions 
//+	manager.
//+
//+	Blocks may be cached for the public user to allow faster loading of pages and increase overall 
//+	efficiency.  Some blocks, such as search results, should not be cached.

//--------------------------------------------------------------------------------------------------
//|	load a block template file
//--------------------------------------------------------------------------------------------------
//arg: fileName - relative to installPath [string]

function loadBlock($fileName) {
	global $theme, $session;
	$session->msg('deprecated: loadBlock(...) => $theme->loadBlock', 'bug');
	$block = $theme->loadBlock($fileName);
	return $block;
}

//--------------------------------------------------------------------------------------------------
//|	save a block, deprecated TODO: replace with filePutContents
//--------------------------------------------------------------------------------------------------
//arg: fileName - absolute fileName [string]
//arg: raw - file contents [string]

function saveBlock($fileName, $raw) {
	global $theme, $session;
	$session->msgAdmin('deprecated: saveBlock(...) => \$theme->saveBlock(...)', 'bug');	
	$r = $theme->saveBlock($fileName, $raw);
	return $r;
}

//--------------------------------------------------------------------------------------------------
//|	expand all blocks within a string
//--------------------------------------------------------------------------------------------------
//arg: txt - text containing block tags to be expanded [string]
//arg: calledBy - newline delimited list of parents, set to empty string [string]
//returns: txt with blocks recusively expanded [string]
//: calledBy is used to prevent infinite recursion, newline delimited list of parents

function expandBlocks($txt, $calledBy) {
	global $theme, $session;
	$session->msgAdmin('deprecated: expandBlocks(...) => \$theme->expandBlocks(...)', 'bug');	
	$txt = $theme->expandBlocks($txt, $calledBy);
	return $txt;
}

//--------------------------------------------------------------------------------------------------
//|	read block to extract api, method and arguments
//--------------------------------------------------------------------------------------------------
//arg: block - a block tag [string]

function blockToArray($block) {
	global $theme, $session;
	$session->msgAdmin('deprecated: blockToArray(...) => \$theme->blockToArray(...)', 'bug');	
	$ba = $theme->blockToArray($block);
	return $ba;
}

//--------------------------------------------------------------------------------------------------
//|	extract all blocks from a piece of text and return an array
//--------------------------------------------------------------------------------------------------
//arg: txt - text or HTML which may contain block tags [string]

function findUniqueBlocks($txt) {
	global $theme, $session;
	$session->msgAdmin('deprecated: findUniqueBlocks(...) => \$theme->findUniqueBlocks(...)','bug');
	$block = $theme->findUniqueBlocks($txt);
	return $blocks;
}

//--------------------------------------------------------------------------------------------------
//|	a get block API's filename
//--------------------------------------------------------------------------------------------------
//arg: module - module name [string]
//arg: fn - view name [string]

function getBlockApiFile($module, $fn) {
	global $theme, $session;
	$session->msgAdmin('deprecated: getBlockApiFile(...) => \$theme->getBlockApiFile(...)', 'bug');
	$filename = $theme->getBlockApiFile($module, $fn);
	return $filename;
}

//--------------------------------------------------------------------------------------------------
//|	execute a block
//--------------------------------------------------------------------------------------------------
//arg: ba - block tag data [array]
//: this is quite an old function, from before views were separated into their own files

function runBlock($ba) {
	global $theme, $session;
	$session->msgAdmin('deprecated: runBlock(...) => \$theme->runBlock(...)', 'bug');
	$block = $theme->runBlock($ba);
	return $block;
}

//--------------------------------------------------------------------------------------------------
//|	remove blocks from a string (TODO: use a regex)
//--------------------------------------------------------------------------------------------------
//arg: txt - text or HTML which may contain blocks [string]
//: useful for summaries, text snippets, etc

function strip_blocks($txt) {
	global $theme, $session;
	$session->msgAdmin('deprecated: strip_blocks(...) => \$theme->stripBlocks(...)', 'bug');
	$txt = $theme->stripBlocks($txt);
	return $txt;
}
 
//--------------------------------------------------------------------------------------------------
//|	substitute an array of values for labels in text
//--------------------------------------------------------------------------------------------------
//arg: labels - array of variable names (keys) and values to replace them with [array]

function replaceLabels($labels, $txt) {
	global $session, $theme;
	$session->msgAdmin('deprecated: replaceLabels(...) => $theme->replaceLabels(...)', 'bug');
	$txt = $theme->replaceLabels($labels, $txt);	
	return $txt;
}

?>
