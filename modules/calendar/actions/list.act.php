<?

		require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//*	list all events for a given day, month or year
//--------------------------------------------------------------------------------------------------
//TODO: replace this with three different actions for listing dats, months and years
//TODO: set cache/spider/robots controls for listings without entries to index/store

	//----------------------------------------------------------------------------------------------
	//	authentication (disallow public access)
	//----------------------------------------------------------------------------------------------
	if (($user->role == 'public') || ($user->role == 'banned')) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	show calendar events for given period
	//----------------------------------------------------------------------------------------------

	$scope = '';
	$period = '';

	if (array_key_exists('day', $req->args)) 
		{ $scope = 'day'; $period = $req->args['day']; }
	if (array_key_exists('month', $req->args)) 
		{ $scope = 'month'; $period = $req->args['month']; }
	if (array_key_exists('year', $req->args)) 
		{ $scope = 'year'; $period = $req->args['year']; }

	//----------------------------------------------------------------------------------------------
	//	default, show this month
	//----------------------------------------------------------------------------------------------

	if ($scope == '') {
		$model = new Calendar_Entry();
	
		$page->load('modules/calendar/actions/month.page.php');
		$page->blockArgs['year'] = date('Y');
		$page->blockArgs['month'] = date('m');
		$page->blockArgs['monthName'] = date('F');
		$page->render();				
	}
	
	//----------------------------------------------------------------------------------------------
	//	show a year
	//----------------------------------------------------------------------------------------------
	
	if ($scope == 'year') { 
		$page->load('modules/calendar/actions/year.page.php');
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
			
			$c = new Calendar_Entry();
			$monthName = $model->getMonthName($bits[1]);
			if ($monthName == false) { $page->do404(); }
		
			$page->load('modules/calendar/actions/month.page.php');
			$page->blockArgs['year'] = $db->addMarkup($bits[0]);
			$page->blockArgs['month'] = $db->addMarkup($bits[1]);
			$page->blockArgs['monthName'] = $monthName;
			$page->render();			
			
		} else { $page->do404(); }
	}

	//----------------------------------------------------------------------------------------------
	//	show a day
	//----------------------------------------------------------------------------------------------

	if ($scope == 'day') {
		$bits = explode('_', $period);
		if (count($bits) == 3) {
	
			$model = new Calendar_Entry();
			$monthName = $model->getMonthName($bits[1]);
			if (false == $monthName) { $page->do404(); }
		
			$page->load('modules/calendar/actions/day.page.php');
			$page->blockArgs['year'] = $db->addMarkup($bits[0]);
			$page->blockArgs['month'] = $db->addMarkup($bits[1]);
			$page->blockArgs['day'] = $db->addMarkup($bits[2]);
			$page->blockArgs['monthName'] = $monthName;
			$page->render();			
			
		} else { $page->do404(); }
	}
	
?>
