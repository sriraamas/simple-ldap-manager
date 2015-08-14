  <form id="adPassForm" action="/admin/resetpwd.php" method="POST" data-abide>
  <div>
     <?php 
      include("admin_login.php");
      ?>
     <br>
    </div>
  <div >
      <p class="sectionHead">Reset Password</p>
      <div class="row">
      <div class="large-6 small-12 push-1 columns">
        <label for="uname">Username
          <input name = "uname" required id="uname"  type="text" />
        </label>
        <small class="error">Username cannot be empty!</small>
      </div>
    </div>
    <div class="row">
      <div class="large-6 small-12 push-1 columns">
        <label for="newPwd">New Password
          <input id="newPwd" required name ="newPwd"  data-abide-validator="isStrong" type="password" />
        </label>
        <small class="error">New Password must have a minimum length of 8 characters.</small>
      </div>
      
    </div>
    <div class="row">
      <div class="large-6 small-12 push-1 columns">
        <label for="cnfNewPwd">Confirm New Password
          <input id="cnfNewPwd" required name ="cnfNewPwd"  data-equalto="newPwd" type="password" />
        </label>
        <small class="error">Passwords Must match!</small>
      </div>
    </div>
    <input type="hidden" id="confirm" name ="confirm" value="false">
     <div class="row">
      <div class="large-6 small-6 push-1 columns">
    <input type="submit" value="Reset Password" class="button mybutton radius "/>
      <img src="img/loading.gif" class="loading" id="loading" style="display:none;"/>
    </div>
    </div>
    </div>
  </form>
