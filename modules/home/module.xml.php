<? /*
<module>
    <modulename>home</modulename>
    <version>1.0 </version>
    <revision>0</revision>
    <description>This module stores static and global content such as the home page, terms and conditions pages, default menus, etc.</description>
    <core>yes</core>
    <installed>no</installed>
    <enabled>no</enabled>
    <dbschema></dbschema>
    <search>no </search>
    <dependencies>
	  <dependency>
        <module>aliases</module>
        <module>images</module>
      </dependency>
    </dependencies>
    <models>
      <model>
        <name>Home_Static</name>
		<description>Static pages.</description>
        <permissions>
          <permission>new</permission>
          <permission>show</permission>
          <permission>edit</permission>
          <permission>delete</permission>
        </permissions>
        <relationships>
		  <relationship>creator</relationship>
        </relationships>
      </model>
    </models>
    <defaultpermissions>
		public:p|home|Home_Static|show
		student:p|home|Home_Static|show
		teacher:p|home|Home_Static|show
    </defaultpermissions>
    <blocks>
    </blocks>
</module>
*/ ?>
