<? /*

[[:theme::navtitlebox::label=Award Badge::toggle=divAwardBadge::hidden=yes:]]
<div id='divAwardBadge' style='visibility: hidden; display: none;'>
<form name='fAwardBadge' method='POST' action='%%serverPath%%badges/award/' >
<input type='hidden' name='action' value='awardBadge' />
<input type='hidden' name='userUID' value='%%userUID%%' />
[[:badges::select::varName=badgeUID:]]
<input type='submit' value='&gt;&gt;' />
</form>
</div>
<br/>

*/ ?>
