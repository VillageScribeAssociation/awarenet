<? /*

<script>
	function comments_dismissPolicy() {
		var url = jsServerPath + 'users/customize/';
		var params = 'key=info.comments.policy&value=hide';

		var cbFn = function(responseText, status) {
			if ('200' == status) { $('#divCommentPolicy').hide(); }
			else { alert(responseText); }
		}

		var thrb = "<img src='" + jsServerPath + "themes/clockface/images/throbber-inline.gif'/>";
		$('#sHideCommentPolicy').html('Dismissing tip... ' + thrb);

		kutils.httpPost(url, params, cbFn);
	}
</script>

<div id='divCommentPolicy'>

<div class='sessionmsg'>
<table noborder='noborder' width='100%'>
  <tr>
    <td valign='top' width='42px'>
		<img src='%%serverPath%%themes/%%defaultTheme%%/images/icons/tip.png' />
	</td>
    <td valign='top'>
		<small>
			You can write any comment you like. Remember others will read it. It can be 
			funny, arouse interest or describe what you think. Be careful with judgments or aggressive 
			comments towards other people. If others find your comment offensive it will be removed.
			<span id='sHideCommentPolicy'><a href='javascript:comments_dismissPolicy();'>[hide]</a>
		</small>
	</td>
  </tr>
</table>
</div>


<div class='spacer'></div>
</div>

*/ ?>
