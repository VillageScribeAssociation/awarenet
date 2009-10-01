<? /*
[[:users::summarynav::userUID=%%friendUID%%::extra=(relationship; %%relationship%%):]]
<form name='confirmFriend' method='POST' action='/users/confirmfriend/' />
<input type='hidden' name='action' value='confirmFriendReq' />
<input type='hidden' name='friendUID' value='%%friendUID%%' />
What is your relationship to [[:users::namelink::userUID=%%friendUID%%:]]?
<select name='relationship'>
  <option value='friend'>friend</option>
  <option value='family'>family</option>
  <option value='boyfriend'>boyfriend</option>
  <option value='girlfriend'>girlfriend</option>
  <option value='penpal'>penpal</option>
  <option value='teammate'>teammate</option>
  <option value='co-worker'>co-worker</option>
  <option value='acquaintance'>acquaintance</option>
  <option value='spouse'>spouse</option>
</select><br/>
<input type='submit' value='confirm friend' />
</form>

<hr/>
*/ ?>
