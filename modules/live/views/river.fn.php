<?

//--------------------------------------------------------------------------------------------------
//|	experimental live 'river' feature
//--------------------------------------------------------------------------------------------------
//arg: rivermodule - module on which block is constructed [string]
//arg: riverview - view showing individual pages [string]
//opt: mod - alias of 'rivermodule' [string]
//opt: view - alias of 'riverview' [string]
//opt: riverpagevar - 'page' variable to be incremented, default is 'pageno' [string]
//opt: pv - alias of 'riverpagevar' [string]
//opt: allow - list of arguments to pass on to the incremented block, pipe separated [string]

function live_river($args) {
	global $kapenta;
	global $theme;

	$riverpagevar = 'pageno';		//%	pagination variable [string]
	$allow = array();				//%	arguments to pass forward [array]
	$html = '';						//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('mod', $args)) { $args['rivermodule'] = $args['mod']; }
	if (false == array_key_exists('rivermodule', $args)) { return '(module not specified)'; }

	if (true == array_key_exists('view', $args)) { $args['riverview'] = $args['view']; }
	if (false == array_key_exists('riverview', $args)) { return '(view not specified)'; }

	if (true == array_key_exists('pv', $args)) { $args['riverpagevar'] = $args['pv']; }
	if (true == array_key_exists('riverpagevar', $args)) { $riverpagevar = $args['riverpagevar']; }

	if (true == array_key_exists('allow', $args)) { $allow = explode('|', $args['allow']); }

	$rivermodule = $args['rivermodule'];
	$riverview = $args['riverview'];

	//----------------------------------------------------------------------------------------------
	//	make the thing
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/live/views/river.block.php');
	$riverargs = array();

	foreach($args as $key => $value) { 
		if (true == in_array($key, $allow)) {
			$riverargs[] = $key . '=' . $value; 
		}
	}

	$labels = array(
		'riverUID' => $kapenta->createUID(),
		'rivermodule' => $rivermodule,
		'riverview' => $riverview,
		'riverargs' => implode('::', $riverargs),
		'riverpagevar' => $riverpagevar
	);

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
