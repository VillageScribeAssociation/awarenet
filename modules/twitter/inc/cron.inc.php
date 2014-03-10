<?

	//	done like this because includes from this lib cause 

	if (version_compare(PHP_VERSION, '5.0.0') >= 0) {
		require_once($kapenta->installPath . 'modules/twitter/models/tweet.mod.php');
		require_once($kapenta->installPath . 'modules/twitter/inc/send.inc.php');
	}

//--------------------------------------------------------------------------------------------------
//*	processes run regularly to keep things tidy
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	ten minute cron
//--------------------------------------------------------------------------------------------------
//returns: HTML report of any actions taken [string]

function twitter_cron_tenmins() {
		global $kapenta;
		global $kapenta;
		global $kapenta;


	if ('yes' !== $kapenta->registry->get('twitter.enabled')) { return "Twitter not enabled.<br/>\n"; }

	$report = "<h2>twitter_cron_tenmins</h2>\n";	//%	return value [string]

	if (version_compare(PHP_VERSION, '5.0.0') >= 0) {
		$report .= "PHP Version requirement met.";
	} else {
		$report .= "PHP Version requirement not met, must be 5+.";
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//	send any unsent tweets if twitter is enabled on this node
	//----------------------------------------------------------------------------------------------

	$conditions = array("status='new'");
	$range = $kapenta->db->loadRange('twitter_tweet', '*', $conditions, 'createdOn ASC');

	foreach($range as $item) {
		$model = new Twitter_Tweet($item['UID']);
		$model->status = 'sent';
		$model->save();

		$report .= twitter_send($model->content);

		if (false !== strpos($result, '<ok/>')) {
			$model->status = 'fail';
			$model->save();
		}

		sleep(20);
	}

	//$sql = "update twitter_tweet set status='sent' where status='new'";
	//$kapenta->db->query($sql);

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $report;

}

//--------------------------------------------------------------------------------------------------
//|	daily cron
//--------------------------------------------------------------------------------------------------
//returns: HTML report of any actions taken [string]

function twitter_cron_daily() {
	global $kapenta;
	global $kapenta;
	global $theme;

	if ('yes' != $kapenta->registry->get('twitter.enabled')) { return "Twitter not enabled.<br/>\n";}

	$report = "<h2>twitter_cron_daily</h2>\n";	//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	poll modules for a dilay report of awareNet activity
	//----------------------------------------------------------------------------------------------
	$date = gmdate("Y-m-d", time() - (60 * 60 * 12));	// twelve hours ago, yesterdays report
	$msg = $theme->expandBlocks('[[:twitter::daily::date=' . $date . ':]]', '');

	if ('' !== $msg) {

		$args = array(
			'refModule' => 'home',
			'refModel' => 'home_static',
			'refUID' => $kapenta->registry->get('home.frontpage'),
			'message' => $date . ' - ' . $msg
		);

		$kapenta->raiseEvent('twitter', 'microblog_event', $args);
		$report .= "Sending Tweet: $msg<br/>\n";

	} else {

		$report .= "Nothing to report, daily status tweet not sent.<br/>\n";

	}

	return $report;
}

?>
