<? /*
[[:users::summarynav::userUID=%%userUID%%::extra=(relationship; %%relationship%%):]]
<form name='confirmFriend' method='POST' action='%%serverPath%%users/confirmfriend/' />
<input type='hidden' name='action' value='confirmFriendReq' />
<input type='hidden' name='friendUID' value='%%userUID%%' />
What is your relationship to [[:users::namelink::userUID=%%userUID%%:]]?
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
</select>
<input type='submit' value='confirm friend' />
</form>

<form name='ignoreFriendRequest' method='POST' action='%%serverPath%%users/removefriend/'>
<input type='hidden' name='action' value='ignoreRequest' />
<input type='hidden' name='friendUID' value='%%userUID%%' />
<input type='submit' value='ignore this request'>
</form>

<hr/>
*/ ?>
