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
	if (($kapenta->user->role == 'public') || ($kapenta->user->role == 'banned')) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	show calendar events for given period
	//----------------------------------------------------------------------------------------------

	$scope = '';
	$period = '';

	if (array_key_exists('day', $kapenta->request->args)) 
		{ $scope = 'day'; $period = $kapenta->request->args['day']; }
	if (array_key_exists('month', $kapenta->request->args)) 
		{ $scope = 'month'; $period = $kapenta->request->args['month']; }
	if (array_key_exists('year', $kapenta->request->args)) 
		{ $scope = 'year'; $period = $kapenta->request->args['year']; }

	//----------------------------------------------------------------------------------------------
	//	default, show this month
	//----------------------------------------------------------------------------------------------

	if ($scope == '') {
		$model = new Calendar_Entry();
	
		$kapenta->page->load('modules/calendar/actions/month.page.php');
		$kapenta->page->blockArgs['year'] = date('Y');
		$kapenta->page->blockArgs['month'] = date('m');
		$kapenta->page->blockArgs['monthName'] = date('F');
		$kapenta->page->render();				
	}
	
	//----------------------------------------------------------------------------------------------
	//	show a year
	//----------------------------------------------------------------------------------------------
	
	if ($scope == 'year') { 
		$kapenta->page->load('modules/calendar/actions/year.page.php');
		$kapenta->page->blockArgs['scope'] = $scope;
		$kapenta->page->blockArgs['year'] = $period;
		$kapenta->page->blockArgs['nextyear'] = ($period + 1);
		$kapenta->page->blockArgs['prevyear'] = ($period - 1);
		$kapenta->page->render();
	}

	//----------------------------------------------------------------------------------------------
	//	show a month
	//----------------------------------------------------------------------------------------------
	
	if ($scope == 'month') {
		$bits = explode('_', $period);
		if (count($bits) == 2) {
			
			$model = new Calendar_Entry();
			$monthName = $model->getMonthName($bits[1]);
			if ($monthName == false) { $kapenta->page->do404(); }
		
			$kapenta->page->load('modules/calendar/actions/month.page.php');
			$kapenta->page->blockArgs['year'] = $kapenta->db->addMarkup($bits[0]);
			$kapenta->page->blockArgs['month'] = $kapenta->db->addMarkup($bits[1]);
			$kapenta->page->blockArgs['monthName'] = $monthName;
			$kapenta->page->render();			
			
		} else { $kapenta->page->do404(); }
	}

	//----------------------------------------------------------------------------------------------
	//	show a day
	//----------------------------------------------------------------------------------------------

	if ($scope == 'day') {
		$bits = explode('_', $period);
		if (count($bits) == 3) {
	
			$model = new Calendar_Entry();
			$monthName = $model->getMonthName($bits[1]);
			if (false == $monthName) { $kapenta->page->do404(); }
		
			$kapenta->page->load('modules/calendar/actions/day.page.php');
			$kapenta->page->blockArgs['year'] = $kapenta->db->addMarkup($bits[0]);
			$kapenta->page->blockArgs['month'] = $kapenta->db->addMarkup($bits[1]);
			$kapenta->page->blockArgs['day'] = $kapenta->db->addMarkup($bits[2]);
			$kapenta->page->blockArgs['monthName'] = $monthName;
			$kapenta->page->render();			
			
		} else { $kapenta->page->do404(); }
	}
	
?>
