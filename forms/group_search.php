<form id="group_search" action="/admin/groupSearch.php" method="post" data-abide>
  <div>
  <?php 
        include("admin_login.php");
  ?>
  </div>
  <br> 
  <p class="sectionHead">Search Groups</p>
  <div class="row">
  <div class="large-10 small-10 push-1 columns">
          <label class="required" for="groupQuery">Enter your search query here:
          <input type="text" required name="groupQuery"/>
          <small class="error">Query cannot be blank!</small>
          </label>
        </div>
    </div>
  <div class="row">
        <div class="large-6 small-6 push-1 columns">
         <input type="submit" class="button mybutton radius" value="Search AD" />
          <img src="/img/loading.gif" class="loading" id="loading" style="display:none;"/>
        </div>
</div>
    </form>