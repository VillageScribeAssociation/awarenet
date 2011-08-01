<?

//--------------------------------------------------------------------------------------------------
//|	displays a javascript calendar for browsing notifications
//--------------------------------------------------------------------------------------------------
//arg: div - name of div in which this calendar is to be rendered [string]
//arg: year - year (yyyy) [string]
//arg: month - month (mm) 01 to 12 [string]
//opt: refModule - name of a kapenta module to filter notifications to [string]

function calendar_notificationsnavjs($args) {


	$month = 0;		//%	calendar month to start from [int]
	$year = 0;		//%	calendar year to start from [int]
	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('month', $args)) { return '(month not given)'; }
	if (false == array_key_exists('year', $args)) { return '(year not given)'; }
	if (true == array_key_exists('refModule', $args)) { $refModule = $args['refModule']; }
	if (true == array_key_exists('div', $args)) { $div = $args['div']; }

	$year = (int)$args['year'];
	$month = (int)$args['month'];
	//TODO: sanity checking

	$model = new Calendar_Entry();

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	return $html;
}

?>
