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
		  <relationship>member</relationship>
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
		teacher:p|groups|Groups_Group|show
    </defaultpermissions>
</module>
*/ ?>
