<? /*
<module>
    <modulename>groups</modulename>
    <version>1.0 </version>
    <revision>Groups module, all groups belong to a school. </revision>
    <description>Groups module, all groups belong to a school. </description>
    <core>yes </core>
    <installed>no </installed>
    <enabled>no </enabled>
    <search>no</search>
	<models>
      <model>
        <name>groups_group</name>
		<description>Represents a group of users, such as societies, associations, clubs, etc.</description>
        <permissions>
          <permission>new</permission>
          <permission>show</permission>
          <permission>edit</permission>
          <permission>delete</permission>
        </permissions>
        <relationships>
		  <relationship>creator</relationship>
		  <relationship>groupmember</relationship>
		  <relationship>groupadmin</relationship>
        </relationships>
      </model>
      <model>
        <name>Groups_Membership</name>
		<description>Index object associating users to groups.</description>
        <permissions>
          <permission>new</permission>
          <permission>show</permission>
          <permission>edit</permission>
          <permission>delete</permission>
        </permissions>
        <relationships>
		  <relationship>creator</relationship>
		  <relationship>subject</relationship>
        </relationships>
      </model>
    </models>
    <defaultpermissions>
		student:p|groups|groups_group|show
		student:p|groups|groups_group|comments-add
		student:p|groups|groups_group|comments-show
		student:c|groups|groups_group|edit|(if)|groupadmin
		student:c|groups|groups_group|edit|(if)|creator
		student:p|groups|groups_group|announcements-show
		student:c|groups|groups_group|announcements-add|(if)|groupadmin
		student:c|groups|groups_group|announcements-edit|(if)|groupadmin
		student:c|groups|groups_group|announcements-delete|(if)|groupadmin
		student:c|groups|groups_group|images-add|(if)|groupadmin
		student:c|groups|groups_group|images-edit|(if)|groupadmin
		student:c|groups|groups_group|images-delete|(if)|groupadmin

		teacher:p|groups|groups_group|show
		teacher:p|groups|groups_group|comments-add
		teacher:p|groups|groups_group|comments-show
		teacher:c|groups|groups_group|edit|(if)|groupadmin
		teacher:c|groups|groups_group|edit|(if)|creator
		teacher:p|groups|groups_group|new
		teacher:p|groups|groups_group|announcements-show
		teacher:p|groups|groups_group|announcements-add
		teacher:p|groups|groups_group|announcements-edit
		teacher:p|groups|groups_group|announcements-delete
    </defaultpermissions>
</module>
*/ ?>
