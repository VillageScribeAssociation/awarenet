<? /*

<script>
	function sketchpad_dismissTip() {
		var url = jsServerPath + 'users/customize/';
		var params = 'key=info.sketchpad.intro&value=hide';

		var cbFn = function(responseText, status) {
			if ('200' == status) { $('#divSketchpadIntro').hide(); }
			else { alert(responseText); }
		}

		var thrb = "<img src='" + jsServerPath + "themes/clockface/images/throbber-inline.gif'/>";
		$('#sHideSketchpadTip').html('Dismissing tip... ' + thrb);

		kutils.httpPost(url, params, cbFn);
	}
</script>

<div id='divSketchpadIntro'>
[[:theme::navtitlebox::label=Tip:]]

<div class='sessionmsg'>
<table noborder='noborder' width='100%'>
  <tr>
    <td valign='top' width='42px'>
		<img src='%%serverPath%%themes/%%defaultTheme%%/images/icons/tip.png' />
	</td>
    <td valign='top'>
		<small>
			awareNet now has a sketchpad!  This means you can edit, draw on or add text to images
			added in galleries and elsewhere.  To try it out, look for the
			<small><a href='javascript:void(0);'>[scribble on this]</a></small> link underneath the
			image when viewing it on its own page.<br/>

			<a href='%%serverPath%%moblog/Using-the-Sketchpad'>[learn more]</a>

			<span id='spanSketchpadIE' style='display: none;'>
			<br/>
			This feature works in Mozilla Firefox, Google Chrome or any modern browser, <i>but not
			with Microsoft Internet Explorer</i> (the browser you are using right now).  Though many
			features do work with Internet Explorer, we recommend switching to Firefox to get the
			most from awareNet.
			</span>

			<script>
				if ($.browser.msie) { $('#spanSketchpadIE').show(); }
			</script>

			<span id='sHideSketchpadTip'><a href='javascript:sketchpad_dismissTip();'>[hide]</a>
		</small>
	</td>
  </tr>
</table>
</div>

<div class='foot'></div>
<br/>

</div>

*/ ?>
