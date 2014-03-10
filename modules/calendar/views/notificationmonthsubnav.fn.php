<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a month of notifications
//--------------------------------------------------------------------------------------------------
//arg: year - year (yyyy) [string]
//arg: month - month (mm) 01 to 12 [string]
//opt: refModule - name of a kapenta module to filter notifications to [string]

function calendar_notificationmonthsubnav($args) {
	global $kapenta;
	global $theme;

	$month = 0;			//%	calendar month [int]
	$year = 0;			//%	calendar year [int]
	$refModule = '';	//%	module whose events we wish to observe [string]
	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('month', $args)) { return '(month not given)'; }
	if (false == array_key_exists('year', $args)) { return '(year not given)'; }
	if (true == array_key_exists('refModule', $args)) { $refModule = $args['refModule']; }
	if (true == array_key_exists('div', $args)) { $div = $args['div']; }

	if ('current' == $args['year']) { $args['year'] = date('Y'); }
	if ('current' == $args['month']) { $args['month'] = date('m'); }

	$year = (int)$args['year'];
	$month = (int)$args['month'];
	//TODO: sanity checking

	$model = new Calendar_Entry();

	//----------------------------------------------------------------------------------------------
	//	get notifications for this month from the database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = 'YEAR(createdOn)=' . $kapenta->db->addMarkup($year);
	$conditions[] = 'MONTH(createdOn)=' . $kapenta->db->addMarkup($month);
	if ('' != $refModule) { $conditions[] = "refModule='" . $kapenta->db->addMarkup($refModule) . "'"; }

	// SELECT *, DAYOFMONTH(createdOn) as coday FROM notifications_notification 
	// where YEAR(createdOn)=YEAR(2011) AND MONTH(createdOn) = MONTH(06);
	
	$range = $kapenta->db->loadRange('notifications_notification', '*, DAYOFMONTH(createdOn) as coday', $conditions);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$days = array();
	$numDays = $model->daysInMonth($month, $year);
	for ($i = 1; $i <= $numDays; $i++) {
		$days[$i] = array(
			'bgcolor' => '#bbbbbb',
			'count' => 0,
			'label' => 0
		);
	}

	foreach($range as $item) {
		//print_r($item); echo "<br/>";
		$days[(int)$item['coday']]['count'] += 1;
		$days[(int)$item['coday']]['label'] += 1;
		$days[(int)$item['coday']]['bgcolor'] = '#7777bb';
	}

	$html = $model->drawMonthTable($month, $year, $days, 'small');

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------

	return $html;
}


//--------------------------------------------------------------------------------------------------

?>
