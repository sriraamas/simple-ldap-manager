<form id="manageUser" action="/admin/manageUser.php" method="POST" data-abide>
     <div>
       <?php 
        include("admin_login.php");
      ?>
     </div>
     <br>
     <div>
        <p class="sectionHead">Manange Users</p>
      <div class="row">
        <div class="large-6 small-12 push-1 columns">
          <label class="required" for="uname">Username
            <input type="text" name="uname" required id="uname" />
          </label>
          <small class="error">Username cannot be blank!</small>
        </div>
      </div>
      <div class="row">
        <div class="large-6 small-6 push-1 columns">
          <input type="submit" value="Manage" class="button radius mybutton"/>
          <img src="/img/loading.gif" class="loading" id="loading" style="display:none;"/>
        </div>
      </div>
      </div>
</form>
