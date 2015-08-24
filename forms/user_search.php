<form  id="user_search" action="/admin/userSearch.php" method="post" data-abide>
  <div >
  <?php 
        include("admin_login.php");
  ?>
  </div>
  <br> 
  <p class="sectionHead">Search Users</p>
<div class="row">
      <div class="large-10 small-10 push-1 columns">
        <label>Enter your search query here:
        <input  type="text" required name="userQuery" id="basic"/>
        <small class="error">Query cannot be blank!</small>
        </label>
      </div>
</div>
<div class="row">
        <div class="large-6 small-6 push-1 columns">
         <input type="submit" class="button radius mybutton" value="Search AD" />
        <img src="img/loading.gif" class="loading" id="loading" style="display:none;"/>

        </div>
</div>
</form>
