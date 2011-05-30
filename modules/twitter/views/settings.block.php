<? /*

<h2>Register your app / site</h2>

From <a href='http://articles.sitepoint.com/article/oauth-for-php-twitter-apps-part-1'>sitepoint's guide</a>:

<p>To register your app, visit <a href='http://dev.twitter.com/apps'>http://dev.twitter.com/apps</a> and complete the form, most of which is quite straightforward - though there are a couple of fields that may throw you:</p>

<ul>
  <li>Callback URL may be left blank (there’s no requirement for your application to authenticate users via a web interface).</li>
  <li>Default Access type refers to the access your application will have to Twitter. Set it to Read & Write (allows complete access).</li>
  <li>Application Type should be changed to Client, even though this application may be run by a cron job simulating a web browser hit, or via a behind-the-scenes trigger from your website, a user will never directly interact with it through a browser.</li>
</ul>

<p>Once your app is registered, you’ll be provided with an OAuth Consumer key and Consumer secret. These two strings are the basis for our OAuth connection.</p>

<form name='frmTwitterSettingsCS' method='POST' action='%%serverPath%%twitter/settings/'>

<table noborder>
  <tr>
	<td><b>Consumer Key:</b></td>
	<td><input type='text' name='twitter_consumerkey' value='%%twitter.consumerkey%%' size='50' /></td>
  </tr>
  <tr>
	<td><b>Consumer Secret:</b></td>
	<td><input type='text' name='twitter_consumersecret' value='%%twitter.consumersecret%%' size='50' /></td>
  </tr>
  <tr>
	<td><b></b></td>
	<td><input type='submit' value='Set' /></td>
  </tr>
</table>

</form>

<h2>Request Token</h2>

<p>You can fill these in manually, or use the 'register' button.  The regiter form will create a 
link to twitter in your status messages, you must be logged in to twitter with the account you wish 
to use when you click this link.</p>

<form name='frmTwitterSettingsRT' method='POST' action='%%serverPath%%twitter/settings/'>
<table noborder>
  <tr>
	<td><b>Request Token:</b></td>
	<td><input type='text' name='twitter_requiesttoken' value='%%twitter.requesttoken%%' size='50' /></td>
  </tr>
  <tr>
	<td><b>Request Token Secret:</b></td>
	<td><input type='text' name='twitter_requesttokensecret' value='%%twitter.requesttokensecret%%' size='50' /></td>
  </tr>
  <tr>
	<td><b></b></td>
	<td><input type='submit' value='Set' /></td>
  </tr>
</table>
</form>

<form name='frmTwitterSettingsRegiter' method='POST' action='%%serverPath%%twitter/settings/'>
<input type='hidden' name='action' value='register' />
<input type='submit' value='Register' />
</form>

<h2>Validate</h2>

<p>Please enter the pin provided in the 'register' step.</p>

<form name='frmTwitterValidate' method='POST' action='%%serverPath%%twitter/settings/'>
<input type='hidden' name='action' value='validate' />
<b>PIN:</b> <input type='text' name='twitter_pin' value='%%twitter.pin%%' size='8' />
<input type='submit' value='Validate'>
</form>

<h2>Access Token</h2>

<form name='frmTwitterSettingsAT' method='POST' action='%%serverPath%%twitter/settings/'>

<table noborder>
  <tr>
	<td><b>Access Token:</b></td>
	<td><input type='text' name='twitter_accesstoken' value='%%twitter.accesstoken%%' size='50' /></td>
  </tr>
  <tr>
	<td><b>Access Token Secret:</b></td>
	<td><input type='text' name='twitter_accesstokensecret' value='%%twitter.accesstokensecret%%' size='50' /></td>
  </tr>
  <tr>
	<td><b></b></td>
	<td><input type='submit' value='Set' /></td>
  </tr>
</table>
</form>

<h2>Test</h2>

<p>Send a tweet to test settings.</p>

<form name='frmTwitterTest' method='POST' action='%%serverPath%%twitter/settings/'>
<input type='hidden' name='action' value='test' />
<textarea name='tweet' rows='3' cols='65'></textarea><br/>
<input type='submit' value='Tweet'>
</form>


*/ ?>
