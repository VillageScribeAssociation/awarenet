<? /*
<module>
    <modulename>abuse</modulename>
    <version>1</version>
    <revision></revision>
    <description>Provides the facility for users to report content, and actions taken tracked and recorded.</description>
    <core>no</core>
	<dependencies>
	</dependencies>
    <models>
      <model>
        <name>Abuse_Report</name>
		<description>Abuse reports may be filed by users about other objects on the system.</description>
        <permissions>
          <permission>new</permission>
          <permission>show</permission>
          <permission>edit</permission>
          <permission>delete</permission>
          <permission>dismiss</permission>
          <export>abuse-file</export>
        </permissions>
        <relationships>
		  <relationship>creator</relationship>
		  <relationship>owner</relationship>
		  <relationship>moderator</relationship>
        </relationships>
      </model>
    </models>
    <defaultpermissions>
		student:p|abuse|abuse_report|new
		teacher:p|abuse|abuse_report|new
    </defaultpermissions>
</module>
*/ ?>
