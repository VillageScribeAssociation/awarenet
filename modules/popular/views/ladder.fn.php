<?

	require_once($kapenta->installPath . 'modules/popular/models/ladder.mod.php');

//--------------------------------------------------------------------------------------------------
//|	returns n most popular entries on a ladder
//--------------------------------------------------------------------------------------------------
//arg: name - name of ladder to show [string]
//opt: ladder - overrides 'name' if present [string]
//opt: num - max number of items to return (int) [string]

function popular_ladder($args) {
	global $session;

	$txt = '';				//%	return value [string]
	$num = 100;				//%	max number of items to show, TODO: make registry setting [int]

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('ladder', $args)) { $args['name'] = $args['ladder']; }
	if (false == array_key_exists('name', $args)) { return ''; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }

	$model = new Popular_Ladder($args['name'], true);
	if (false == $model->loaded) { 
		//echo "ladder not loaded";
		return ''; 
	}

	//----------------------------------------------------------------------------------------------
	//	make the list
	//----------------------------------------------------------------------------------------------
	foreach($model->entries as $entry) {
		$txt .= $entry . "\n";
		$num--;
		if (0 >= $num) { break; }
	}
	
	$txt = trim($txt);

	return $txt;
}

?>
