<?

//--------------------------------------------------------------------------------------------------
//	list all events for a given day, month or year
//--------------------------------------------------------------------------------------------------

	$scope = '';
	$period = '';

	if (array_key_exists('day', $request['args'])) 
		{ $scope = 'day'; $period = $request['args']['day']; }
	if (array_key_exists('month', $request['args'])) 
		{ $scope = 'month'; $period = $request['args']['month']; }
	if (array_key_exists('year', $request['args'])) 
		{ $scope = 'year'; $period = $request['args']['year']; }

	//----------------------------------------------------------------------------------------------
	//	default, show this month
	//----------------------------------------------------------------------------------------------

	if ($scope == '') {
		require_once($installPath . 'modules/calendar/models/calendar.mod.php');
		$c = new Calendar();
	
		$page->load($installPath . 'modules/calendar/actions/month.page.php');
		$page->blockArgs['year'] = date('Y');
		$page->blockArgs['month'] = date('m');
		$page->blockArgs['monthName'] = date('F');
		$page->render();				
	}
	
	//----------------------------------------------------------------------------------------------
	//	show a year
	//----------------------------------------------------------------------------------------------
	
	if ($scope == 'year') { 
		$page->load($installPath . 'modules/calendar/actions/year.page.php');
		$page->blockArgs['scope'] = $scope;
		$page->blockArgs['year'] = $period;
		$page->blockArgs['nextyear'] = ($period + 1);
		$page->blockArgs['prevyear'] = ($period - 1);
		$page->render();
	}

	//----------------------------------------------------------------------------------------------
	//	show a month
	//----------------------------------------------------------------------------------------------
	
	if ($scope == 'month') {
		$bits = explode('_', $period);
		if (count($bits) == 2) {
		
			require_once($installPath . 'modules/calendar/models/calendar.mod.php');
		
			$c = new Calendar();
			$monthName = $c->getMonthName($bits[1]);
			if ($monthName == false) { do404(); }
		
			$page->load($installPath . 'modules/calendar/actions/month.page.php');
			$page->blockArgs['year'] = sqlMarkup($bits[0]);
			$page->blockArgs['month'] = sqlMarkup($bits[1]);
			$page->blockArgs['monthName'] = $monthName;
			$page->render();			
			
		} else { do404(); }
	}

	//----------------------------------------------------------------------------------------------
	//	show a day
	//----------------------------------------------------------------------------------------------

	if ($scope == 'day') {
		$bits = explode('_', $period);
		if (count($bits) == 3) {
		
			require_once($installPath . 'modules/calendar/models/calendar.mod.php');
		
			$c = new Calendar();
			$monthName = $c->getMonthName($bits[1]);
			if ($monthName == false) { do404(); }
		
			$page->load($installPath . 'modules/calendar/actions/day.page.php');
			$page->blockArgs['year'] = sqlMarkup($bits[0]);
			$page->blockArgs['month'] = sqlMarkup($bits[1]);
			$page->blockArgs['day'] = sqlMarkup($bits[2]);
			$page->blockArgs['monthName'] = $monthName;
			$page->render();			
			
		} else { do404(); }
	}
	
?>
