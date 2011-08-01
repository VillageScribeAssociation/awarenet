<? /*

<script src="%%serverPath%%modules/videos/js/flowplayer-3.2.4.min.js"></script>

<a
	href="%%serverPath%%%%fileName%%"
	style="display:block;width:100%;height:%%height%%px;"
	id="player%%UID%%">
</a>

<script language="JavaScript">
flowplayer(
	"player%%UID%%", 
	"%%serverPath%%modules/videos/flash/flowplayer-3.2.5.swf", 
	{
		plugins: {
		},


		clip: { 
		   autoPlay: false,
		   url: '%%serverPath%%%%fileName%%',
		   coverImage: { url: '%%coverImage%%', scaling: 'orig' } 
		}

	}
);
</script>

*/ ?>
