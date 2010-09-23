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
        <name>Groups_Group</name>
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
		student:p|groups|Groups_Group|show
		student:c|groups|Groups_Group|edit|(if)|groupadmin
		student:c|groups|Groups_Group|edit|(if)|creator
		student:c|groups|Groups_Group|announcements-add|(if)|groupadmin
		student:c|groups|Groups_Group|announcements-edit|(if)|groupadmin
		student:c|groups|Groups_Group|announcements-delete|(if)|groupadmin

		teacher:p|groups|Groups_Group|show
		teacher:c|groups|Groups_Group|edit|(if)|groupadmin
		teacher:c|groups|Groups_Group|edit|(if)|creator
		teacher:p|groups|Groups_Group|new
		teacher:p|groups|Groups_Group|announcements-add
		teacher:p|groups|Groups_Group|announcements-edit
		teacher:p|groups|Groups_Group|announcements-delete
    </defaultpermissions>
</module>
*/ ?>
