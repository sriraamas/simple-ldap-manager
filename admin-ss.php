<!DOCTYPE html>
<?php
setcookie("xsrftoken", "22a7cfaa8a6ba642b3269f32aad8a601", time() + (86400 * 30), "/"); // 86400 = 1 day
?>
<!-- saved from url=(0049)http://foundation.zurb.com/templates/sidebar.html -->
<html class=" js flexbox flexboxlegacy canvas canvastext webgl no-touch geolocation postmessage websqldatabase indexeddb hashchange history draganddrop websockets rgba hsla multiplebgs backgroundsize borderimage borderradius boxshadow textshadow opacity cssanimations csscolumns cssgradients cssreflections csstransforms csstransforms3d csstransitions fontface generatedcontent video audio localstorage sessionstorage webworkers applicationcache svg inlinesvg smil svgclippaths" lang="en" data-useragent="Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2209.0 Safari/537.36"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Simple LDAP Manager</title>
<meta name="description" content="LDAP Self-Service Tool, used internally at Lucid Software">
<meta name="author" content="Naysayers">
<link rel="stylesheet" href="css/foundation.css">
<script src="js/vendor/modernizr.js"></script>
<link rel="stylesheet" href="css/app.css">
<link rel="stylesheet" href="css/jquery.dataTables.min.css">
<link rel="stylesheet" href="css/dataTables.foundation.css">
<meta class="foundation-data-attribute-namespace"><meta class="foundation-mq-xxlarge"><meta class="foundation-mq-xlarge-only"><meta class="foundation-mq-xlarge"><meta class="foundation-mq-large-only"><meta class="foundation-mq-large"><meta class="foundation-mq-medium-only"><meta class="foundation-mq-medium"><meta class="foundation-mq-small-only"><meta class="foundation-mq-small"><style></style><meta class="foundation-mq-topbar"></head>
<body>
<?php include("topnav.phtml");?>
<div>
  <div id="notify" class = "row">
   
  </div>
  <div class = "large-12 small-12 columns" style="width:100%; height:100%;display:table">
  <div class="row" >
    <div class="sidebarDiv">
      <ul id="myTabs" class="tabs vertical" data-options ="deep_linking:true; scroll_to_content:false" data-tab role="tablist" style="padding-top:2%">
        <li class="tab-title mylogo no-hover" role="presentation"><h5><img class="logo maw-100 rs d-n_2" src="https://d2slcw3kip6qmk.cloudfront.net/marketing/logos/lucidsoftware-logo-text-194x42.png" alt="Lucid Software, Inc."></h5></li>
        <li class="sidebar  sideHead">Admin</li>
        <li class="sidebar active" role="presentation"><a href="#panel2-1" role="tab" tabindex="0" aria-controls="panel2-1">Reset Password</a></li>
        <li class="sidebar" role="presentation"><a href="#panel2-2" role="tab" tabindex="1" aria-controls="panel2-2">Revoke VPN</a></li>
        <li class="sidebar" role="presentation"><a href="#panel2-3" role="tab" tabindex="2" aria-controls="panel2-3">Revoke SSH</a></li>
        <li class="sidebar" role="presentation"><a href="#panel2-4" role="tab" tabindex="3" aria-controls="panel2-4">Add User</a></li>
        <li class="sidebar" role="presentation"><a href="#panel2-5" role="tab" tabindex="3" aria-controls="panel2-5">User Search</a></li>
        <li class="sidebar lastitem" role="presentation"><a href="#panel2-6" role="tab" tabindex="4" aria-controls="panel2-6">Groups Search</a></li>
      </ul>
    </div>
    <div class="large-8 small-10 columns">
<div class="tabs-content mycontent">

<section role="tabpanel" aria-hidden="false" class="content active " id="panel2-1">
    <?php include("forms/admin_chpass.php"); ?>
</section>
<section role="tabpanel" aria-hidden="true" class="content" id="panel2-2">
    <?php include("forms/admin_vpn.php");?>
</section>

<section role="tabpanel" aria-hidden="true" class="content" id="panel2-3">
    <?php include("forms/admin_ssh.php"); ?>
</section>
<section role="tabpanel" aria-hidden="true" class="content" id="panel2-4">
    <?php include("forms/admin_newuser.php"); ?>
</section>
<section role="tabpanel" aria-hidden="true" class="content" id="panel2-5">
    <?php include("forms/user_search.php"); ?>
</section>
<section role="tabpanel" aria-hidden="true" class="content" id="panel2-6">
    <?php include("forms/group_search.php"); ?>
</section>

</div> 
    </div>
  </div>
</div>
</div>
<div  class="row" id="searchResult">


</div>
<footer class="row">
<div class="large-12 columns" >

<div class="row">
<div class="large-6 columns">
<!-- <p>© Copyright no one at all. Go to town.</p> -->
</div>

</div>
</div>
</footer>
<!-- <script src="js/vendor//zepto.js"></script> -->
<script src="js/vendor/jquery.js"></script>
<script src="js/foundation.min.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/util.js"></script>
<script src="js/form.js"></script>
<script src="js/admin.js"></script>

<script>
  $(document).ready(function(){
  $('#table_id').DataTable({
    searching:false
  });
});
$(document).foundation({
  abide: {
        live_validate : true, // validate the form as you go
        error_labels: true, // labels with a for="inputId" will recieve an `error` class
        validators :{
          isStrong: function(el,required,parent){
            return (el.value.length >= 8);
        }
    }
  }
});
var doc = document.documentElement;
doc.setAttribute('data-useragent', navigator.userAgent);
</script>
</body></html>