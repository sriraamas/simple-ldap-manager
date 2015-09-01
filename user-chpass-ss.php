<?php
require_once("core/util.php");
$randomStr = randomPassword(16);
setcookie("xsrftoken", $randomStr , time() + (86400 * 30), "/"); // 86400 = 1 day
?>
<!DOCTYPE html>
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
<meta class="foundation-data-attribute-namespace"><meta class="foundation-mq-xxlarge"><meta class="foundation-mq-xlarge-only"><meta class="foundation-mq-xlarge"><meta class="foundation-mq-large-only"><meta class="foundation-mq-large"><meta class="foundation-mq-medium-only"><meta class="foundation-mq-medium"><meta class="foundation-mq-small-only"><meta class="foundation-mq-small"><style></style><meta class="foundation-mq-topbar"></head>
<body>
<?php
include("topnav.phtml")
?>
<div>
  <div  id="notify" class = "row">
  </div>
  <div class = "large-12 small-12 columns" style="width:100%; height:100%;display:table">
  <div class="row" >
    <div class="sidebarDiv">
      <ul class="tabs vertical" data-options ="deep_linking:true; scroll_to_content:false"  role="tablist" style="padding-top:2%">
        <li class="tab-title mylogo no-hover" role="presentation"><h5><img class="logo maw-100 rs d-n_2" src="https://d2slcw3kip6qmk.cloudfront.net/marketing/logos/lucidsoftware-logo-text-194x42.png" alt="Lucid Software, Inc."></h5></li>
        <li class="sidebar  sideHead">Self-Service</li>
        <li class="sidebar active" role="presentation"><a href="#panel2-1" role="tab" tabindex="0" aria-controls="panel2-1">Change Password</a></li>
        <li class="sidebar" role="presentation"><a href="user-vpn-ss.php#panel2-2" role="tab" tabindex="1" aria-controls="panel2-2">VPN Credentials</a></li>
        <li class="sidebar lastitem" role="presentation"><a href="user-ssh-ss.php#panel2-3" role="tab" tabindex="2" aria-controls="panel2-3">SSH Credentials</a></li>
      </ul>

    </div>
    <div class="large-8 small-10 columns">
<div class="tabs-content mycontent">

<section role="tabpanel" aria-hidden="false" class="content active " id="panel2-1">
  <form id="passForm" action="/user/resetpwd.php" method="POST" data-abide>
  <p class="sectionHead">Change Password</p>
    <div class="row">
      <div class="large-6 small-12 push-1 columns">
        <label class="required" for="uname">Username
          <input type="text" required name="uname" tabindex="3" id="uname" />
        </label>
        <small class="error">Username cannot be empty!</small>
      </div>
    </div>
    <div class="row">
      <div class="large-6 small-12 push-1 columns">
        <label class="required" for="pwd">Old Password
          <input name = "pwd" required id="pwd"  tabindex="4" type="password" />
        </label>
        <small class="error">Password Cannot be Empty</small>
      </div>
    </div>
    <div class="row">
      <div class="large-6 small-12 push-1 columns">
        <label class="required" for="newPwd">New Password
          <input id="newPwd" required name ="newPwd" tabindex="5" type="password" />
        </label>
        <small class="error">New Password cannot be blank</small>
      </div>
      <div class="large-5 small-12 columns">
        <br>
        <div class="help">
        <!-- <a href="#" tabindex="8" > Password Help </a> -->
        <?php
        require_once("core/util.php");
        $help = getConfig("pwdHelp");
        foreach($help as &$h){
          $h = "<li>".$h."</li>";
        }
        $helpText = "<ul>".implode($help)."</ul>";
        echo "<span data-tooltip aria-haspopup='true' class='has-tip' data-options='show_on:large' title='$helpText'>Password Help</span>";
        ?>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="large-6 small-12 push-1 columns">
        <label class="required" for="cnfNewPwd">Confirm New Password
          <input id="cnfNewPwd" required name ="cnfNewPwd" tabindex="6" data-equalto="newPwd" type="password" />
        </label>
        <small class="error">Passwords Must match!</small>
      </div>
    </div>
     <div class="row">
      <div class="large-6 small-12 push-1 columns">
    <input type="submit" value="Change" tabindex="7" class="button mybutton radius "/>
      <img src="/img/loading.gif" class="loading" id="loading" style="display:none;"/>

    </div>
  </form>
  </div>
</section>

</div> 
    </div>
  </div>
</div>
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
<script src="js/util.js"></script>
<script src="js/form.js"></script>
<script src="js/user.js"></script>
<script>
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
