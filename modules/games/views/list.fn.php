<?php

//--------------------------------------------------------------------------------------------------
//|	list game summaries
//--------------------------------------------------------------------------------------------------
//;	Games are recorded in the registry as a comma separated list, and displayed in that order

function games_list($args) {
	global $user;
	global $theme;
	global $kapenta;	
	global $kapenta;

	$html = '';									//%	return value [string]
	$list = $kapenta->registry->get('games.list');		//%	comma separated list [string]
	$games = explode(",", $list);				//%	set of games [array:string]
	
	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if (('public' == $user->role) || ('banned' == $user->role)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	foreach($games as $game) {
		$game = trim($game);
		$summaryFile = 'modules/games/views/summary_' . $game . '.fn.php';
		if (('' != $game) && (true == $kapenta->fs->exists($summaryFile))) {
			$html .= ''
			 . "<div class='block'>"
			 . "[[:games::summary_" . $game . ":]]\n"
			 . "</div><br/>";
		}
	}

	return $html;
}

?>
