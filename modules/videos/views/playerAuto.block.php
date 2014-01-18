<? /*

<script src='%%serverPath%%modules/videos/js/flowplayer-3.2.6.min.js'></script>

<a
	href="%%serverPath%%%%fileName%%"
	style="display:block;width:%%width%%px;height:%%height%%px;"
	id="player%%UID%%r%%rand%%">

</a>

<!-- deprecated: splash image -->
<!-- <img src='%%coverImage%%' style='width: %%width%%px; height: %%height%%px;' /> -->

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
		   autoPlay: true,
		   url: '%%serverPath%%%%fileName%%'
		},

		canvas:  { backgroundImage: 'url(%%coverImage%%)' },
		
		onBeforeClick: function() {
        	alert("player loaded.");
    	} 
	}
);
</script>

<small>
%%browserLink%%
%%like%%
%%extra%% 
</small>

<span style='float: right;'>
  <small>
    [[:abuse::reportlink::refModule=videos::refModel=videos_video::refUID=%%UID%%:]]
  </small>
</span>
<br/>
*/ ?>
