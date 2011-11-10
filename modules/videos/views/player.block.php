<? /*

<script src="%%serverPath%%modules/videos/js/flowplayer-3.2.6.min.js"></script>

<a
	href="%%serverPath%%%%fileName%%"
	style="display:block;width:100%;height:%%height%%px;"
	id="player%%UID%%">
</a>

<script language="JavaScript">
flowplayer(
	"player%%UID%%", 
	"%%serverPath%%modules/videos/flash/flowplayer-3.2.7.swf", 
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

%%browserLink%%

*/ ?>
