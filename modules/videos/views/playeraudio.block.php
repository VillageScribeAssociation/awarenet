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
			audio: {
				url: '%%serverPath%%modules/videos/flash/flowplayer.audio-3.2.2.swf'
			}
		},


		clip: { 
		   autoPlay: false,
		   url: '%%serverPath%%%%fileName%%'
		},

		canvas:  {
			// configure background properties
			backgroundImage: 'url(%%coverImage%%)'
		}


	}
);
</script>

*/ ?>
