<?

//--------------------------------------------------------------------------------------------------
//|	show notifications from a given range of dates
//--------------------------------------------------------------------------------------------------
//arg: startDate - a date in YYYY-MM-DD format [string]
//arg: endDate - a date in YYYY-MM-DD format [string]
//opt: refModule - name of a kapenta module which owns the notification [string]

function notifications_daterange($args) {
	global $user;
	global $theme;

	$startDate = '';		//%	notifications from, inclusive, YYYY-MM-DD [string]
	$endDate = '';			//%	notifications to, inclusive, YYYY-MM-DD [string]
	$refModule = '';		//%	
	
	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (('public' == $user->role) || ('banned' == $user->role)) { return ''; }

	if (true == array_key_exists('startDate', $args)) { $startDate = $args['startDate']; }
	if (true == array_key_exists('endDate', $args)) { $startDate = $args['endDate']; }

	

	//TODO: create a permissions set here

	

	return $html;
}


?>
