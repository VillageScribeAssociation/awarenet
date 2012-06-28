<? /*

<script src='%%serverPath%%modules/videos/js/flowplayer-3.2.6.min.js'></script>

<a
	href="%%serverPath%%%%fileName%%"
	style="display:block;width:%%width%%px;height:%%height%%px;"
	id="player%%UID%%r%%rand%%">
</a>

<script language="JavaScript">
flowplayer(
	"player%%UID%%r%%rand%%", 
	{
		src:"%%serverPath%%modules/videos/flash/flowplayer-3.2.7.swf", 
		wmode:"opaque"
	},
	{
		plugins: {
		},

		clip: { 
		   autoPlay: false,
		   url: '%%serverPath%%%%fileName%%'
		},

		canvas:  { backgroundImage: 'url(%%coverImage%%)' }

	}
);
</script>

%%browserLink%% %%like%% %%extra%%
<br/>
*/ ?>
