<? /*

<script src="%%serverPath%%modules/videos/js/flowplayer-3.2.4.min.js"></script>

<a
	href="%%serverPath%%%%fileName%%"
	style="display:block;width:100%;height:%%height%%px;"
	id="player">
</a>

<script language="JavaScript">
flowplayer(
	"player", 
	"%%serverPath%%modules/videos/flash/flowplayer-3.2.5.swf", 
	{
		plugins: {
			audio: {
				url: '%%serverPath%%modules/videos/flash/flowplayer.audio-3.2.2.swf'
			}
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
