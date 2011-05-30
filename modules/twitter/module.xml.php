<? /*

<module>
    <modulename>twitter</modulename>
    <version>1.0</version>
    <revision>0</revision>
    <description>For recording, queueign and sending status updates to twitter.</description>
    <core>no</core>
    <installed>no</installed>
    <enabled>yes</enabled>
    <search>no</search>
    <dependencies>
    </dependencies>
    <models>
      <model>
        <name>Twitter_Tweet</name>
		<description>For storing status updates until sent.</description>
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
    </defaultpermissions>
    <blocks>
    </blocks>
</module>

*/ ?>
