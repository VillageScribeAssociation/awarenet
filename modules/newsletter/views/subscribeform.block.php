<? /*

<script language='Javascript'>

	function newsletter_validateSignup() {
		var emailAddress = document.getElementById('txtEmailSignup').value;
		var breadCrumb = document.getElementById('spanBreadcrumb');

		breadCrumb.innerHTML = ''
		 + '<span>'
		 + '<img src="%%serverPath%%themes/swiftnews/images/throbber-inline.gif" />&nbsp;'
		 + 'Signing up <a href="#">' + emailAddress + '...'
		 + '</span>';

		var params = 'email=' + emailAddress;
		var url ='%%serverPath%%newsletter/signupajax/'

		if ('' == kutils.trim(emailAddress)) {
			breadCrumb.innerHTML = '<span>Please enter an email address to sign up.</span>';
			return;
		}

		var cbFn = function(responseText, statusText) {
			if ('200' == statusText) {
				breadCrumb.innerHTML = '<span>' + responseText + '</span>';
			} else {
				breadCrumb.innerHTML = '<span>Signup could not be completed, please try again later. :(</span>';				
			}
		};

		kutils.httpPost(url, params, cbFn);

	}

</script>

<div style='float: right'>
<form name='fromSignup' method='POST' action='%%serverPath%%newsletter/signup/'>
<input type='text' name='email' id='txtEmailSignup' style='width: 200px; border-radius: 2px;' placeholder='your@email.com'/>
<input type='button' value='Sign Up!' onClick='newsletter_validateSignup();' />&nbsp;&nbsp;
</form>
</div>

*/ ?>
