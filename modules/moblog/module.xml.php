<? /*
<module>
    <modulename>moblog</modulename>
    <version>1.0</version>
    <revision>For producing the moblog (user blogs agregated by school, group, etc).</revision>
    <description>For producing the moblog (user blogs agregated by school, group, etc).</description>
    <core>yes</core>
    <installed>no</installed>
    <enabled>yes</enabled>
    <dbschema></dbschema>
    <search>no</search>
    <models>
      <model>
        <name>Moblog_Post</name>
		<description>Individual blog posts.</description>
        <permissions>
          <permission>show</permission>
          <permission>list</permission>
          <permission>edit</permission>
          <permission>new</permission>
        </permissions>
        <relationships>
		  <relationship>creator</relationship>
        </relationships>
      </model>
    </models>
    <defaultpermissions>
		student:p|moblog|Moblog_Post|show
		student:c|moblog|Moblog_Post|edit|(if)|creator
		teacher:p|moblog|Moblog_Post|show
		teacher:c|moblog|Moblog_Post|edit|(if)|creator
    </defaultpermissions>
</module>
*/ ?>
