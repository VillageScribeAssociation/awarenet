<? /*

[[:theme::navtitlebox::label=Make Friend Request::toggle=divFriendRequest::hidden=yes:]]
<div id='divFriendRequest' style='display: none;'>
<form name='friendRequest' method='POST' action='%%serverPath%%users/addfriendrequest/' >
<input type='hidden' name='action' value='addFriendReq' />
<input type='hidden' name='friendshipUID' value='%%friendshipUID%%' />
<input type='hidden' name='friendUID' value='%%friendUID%%' />
<input type='hidden' name='return' value='%%return%%' />
What is your relationship to [[:users::namelink::userUID=%%friendUID%%:]]?
<select name='relationship'>
  <option value='friend'>friend</option>
  <option value='family'>family</option>
  <option value='scholar'>scholar</option>
  <option value='teacher'>teacher</option>
  <option value='boyfriend'>boyfriend</option>
  <option value='girlfriend'>girlfriend</option>
  <option value='penpal'>penpal</option>
  <option value='teammate'>teammate</option>
  <option value='co-worker'>co-worker</option>
  <option value='acquaintance'>acquaintance</option>
  <option value='spouse'>spouse</option>
</select><br/>
<input type='submit' value='ask to be added to friend list' />
</form>
</div>
<div class='foot'></div>
<br/>

*/ ?>
