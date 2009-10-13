<?

	$str = "

Lockerz is the place to go to buy the coolest stuff at the lowest prices, watch exclusive video, discover new music, play the hottest games, hang out with your friends - and get rewarded for just about EVERYTHING you do on the site.

Our mission is to be your daily habit, not a site for your parents or grandparents looking for their long-lost friends from Kindergarten.

One other thing you'll love about Lockerz are PTZ (or 'Pointz'). PTZ are Lockerz' proprietary loyalty 'currency' that you earn whenever you buy, watch, play, share, invite friends, or even just show up on Lockerz. Redeem PTZ at the PTZ Place for incredible prizes, great products and dream experiences.

Too Good to Be True? Why are we doing this?  To thank you for joining early.  To build a Lockerz community of cool, stylish, cutting-edge people who love to buy great brands.  To give you a taste of how PTZ will work when we go live this fall.  And to test out different prizes and PTZ levels.  That’s key.  This version of PTZ Place is a test.  PTZ levels will change when the full Lockerz site launches.

Lockerz is set to launch sometime in the Fall 09. Members who sign up now will get to explore and help shape the site before the launch. We hope you'll join us.
	";

	if ( (array_key_exists('var1', $_POST) == true) && ($_POST['var1'] == 'moose') ) {
		echo str_replace("\n", "<br/>\n", strrev($str));
	} else {
		echo str_replace("\n", "<br/>\n", $str);
	}

?>
