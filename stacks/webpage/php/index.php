<?php
  include_once 'header.php';
  /*
    Page flow ->
    header.php -> func::checkLoginStatus() -> Index -> Loginbox.php -> Login.php ->

  */
?>
  <body>
    <div class="main_page">
      <div class="page_header floating_element">
        <a href="/pdf/resume2018.pdf"><img src="/img/me.png" class="floating_element"/></a>
        <span class="floating_element">
          Eric Weissgerber
        </span>
      </div>
      <div class="content_section floating_element">
        <div class="section_header section_header_red">
          <div id="about"></div>
          <?php
            if (!func::checkLoginState($db))
            {
              echo '<center><H1><a href="loginform.php">LOGIN</a></H1></center>';
            }
            else
            {
              echo "<center><b>Welcome ".$_SESSION['username']."! </center></b>";
            }
          ?>
        </div>


	<center>Not a member?  <a href="register.php">Sign up</a></center>
  <center><a href="/lib/phpinfo.php">phpinfo</a></center>
    <div class="content_section_text">
      <p>
        This is my personal webpage (basically a modified ubuntu default page).
    		Below you will find some links to some of the files that I am hosting here.
    		Eventually I will be posting my resume and some other biographical information for your reading
    		pleasure.
      </p>
      <p>
        Disclaimer: I, in no way, authorize illegal distribution of copyrighted material.  Files that you find here
		    are only a proof of concept of autoindexing of filepaths and in no way, are meant to enable illegal distribution.
		    I am not responsible for the actions of the users of this site.
	  </p>
<!--
	  <p>
	  <center>
	  <a href="Recent.Downloads/">Recent Downloads</a> | <a href="Software/">Software</a> | <a href="Games/">Games</a> |
	  <a href="Movies/">Movies</a> | <a href="Classics/">Classic Movies</a> | <a href="tv/">TV</a> |
	  <a href="Music/">Music</a> | <a href="Classic.TV/">Classic TV</a> | <a href="Star.Trek/">Star Trek</a>
	  </center>
	  </p>
-->
    </div>

    <div class="section_header">
      <div id="docroot"></div>
        About Me
      </div>

      <div class="content_section_text">
        <p>
		      If you are looking for a consultation or to employ my services, you can find my resume
		      and qualifications <a href="/pdf/resume2018.pdf">here</a>.
        </p>
        <p>
		      I enjoy astronomy, physics, debate, new technology, science fiction and wild theories.
		      I'm very technology saavy and enjoy learning everything I possibly can.  I do my best to
          stay on top of emerging hardware, architecture, virtualization, networking technology,
          search algorithms, data storage structures, and
          basically anything having to do with computers and how people interact with them.
        </p>
      </div>

 <div class="section_header section_header_red">
          <div id="changes"></div>
                Work Experience
        </div>
        <div class="content_section_text">
      	  <ul><p><b>Linux Administration</b>
	    <li>Daily use of Debian, Red Hat Enterpriese Linux, Ubuntu</li>
	    <li>Familiar with Arch and Gentoo</li>
            <li>Linux server administration (http, smtp, pop3, imap, dns, dhcp, rsync, ftp, ssh, nfs)</li>
            <li>General network administration</li>
            <li>Apache + countless modules such as (ie: mod_rewrite)</li>
            <li>PHP + MySQL (LAMP) webhosting platform</li>
            <li>NIS, Kerberos, OpenLDAP network information and directory services</li>
            <li>Samba file server</li>
            <li>Postfix mail server</li>
            <li>BIND DNS server</li>
            <li>IPTables Linux firewall</li>
            <li>VMWare, KVM-qemu, Virtual Box, LXC and Docker</li>
            <li>Source Code Management using Git and Subversion</li>
            <li>Linux Software RAID</li>
            <li>Device Mapper and Logical Volume Management</li>
            <li>Server specification and build</li>
            I do projects on a time and materials basis or in special cases, a fixed fee. I require partial payment up front in good faith that the client is sincerely interested in the proposed project.
          </ul></p>
</div>
      <div class="section_header">
        <div id="changes"></div>
          System Configuration Overview
      </div>
      <div class="content_section_text">
        <p>This website's access analysis is located <a href="/analog/Report.html">here</a></p>
	      <p>Now using nginx instead of Apache2</p>
        <div class="section_header">
          <div id="bugs"></div>
                Reporting Problems
        </div>
        <div class="content_section_text">
          <p>
            Please report bugs specific to modules (such as PHP and others)
            to respective packages, not to the web server itself.
          </p>
        </div>
      </div>
    </div>
    <div class="validator">
    </div>
<?php include "footer.php"; ?>
