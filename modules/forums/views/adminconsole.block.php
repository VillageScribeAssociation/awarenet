<? /*

<ul>
  <li><a href='%%serverPath%%admin/'>Merge threads</a> - Put thread's aliases or UIDs into the boxes below and click merge.  The alias is the last part of the thread's URL, after the final '/', excluding any parts with underscores in them.<br/><br/>
	<form name='forumMergeThreads' method='POST' action='%%serverPath%%forums/mergethread/'>
		<input type='hidden' name='action' value='forumMergeThreads' />
		<b>From Thread:</b> <input type='text' name='fromThread' size='10' />
		<b>To Thread:</b> <input type='text' name='toThread' size='10' />
		<input type='submit' value='Merge &gt;&gt;' />
	</form>
	<br/>
  </li>
  <li>
	<a href='%%serverPath%%admin/'>Import project</a> - Converts a project and its comments into a forum thread and replies.<br/><br/>
	<form name='importProject' method='POST' action='%%serverPath%%forums/importproject/'>
		<input type='hidden' name='action' value='importProject' />
		<b>Project:</b> <input type='text' name='project' size='10' />
		<b>into:</b> [[:forums::selectforum::varname=board:]]
		<input type='submit' value='Import &gt;&gt;' />
	</form>
  </li>

</ul>

*/ ?>
