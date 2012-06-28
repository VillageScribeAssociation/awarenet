<? /*
<module>
    <modulename>announcements</modulename>
    <version>1.0</version>
    <revision>0</revision>
    <description>Announcements module, dependant on other modules (schools, groups, etc make announcements).</description>
    <core>yes</core>
    <installed>no</installed>
    <enabled>no</enabled>
    <dbschema></dbschema>
    <search>no</search>
    <dependancy>
    </dependancy>
	<models>
		<model>
			<name>Announcements_Announcement</name>
			<description></description>
			<permissions>
				<permission>new</permission>
				<permission>show</permission>
				<permission>list</permission>
				<permission>delete</permission>
				<export>announcements-add</export>
				<export>announcements-edit</export>
				<export>announcements-show</export>
				<export>announcements-delete</export>
			</permissions>
			<relationships>
				<relationship>creator</relationship>
			</relationships>
		</model>
	</models>
    <defaultpermissions>
		student:p|announcements|announcements_announcement|show
		student:p|announcements|announcements_announcement|list
		student:c|announcements|announcements_announcement|edit|(if)|creator

		teacher:p|announcements|announcements_announcement|announcement-add
		teacher:c|announcements|announcements_announcement|announcement-edit|(if)|creator
		teacher:c|announcements|announcements_announcement|announcement-delete|(if)|creator
    </defaultpermissions>
    <blocks>
    </blocks>
</module>
*/ ?>
