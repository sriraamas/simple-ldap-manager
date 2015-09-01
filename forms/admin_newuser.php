<div>
<form id="newUser" action="/admin/newUser.php" method="POST" data-abide>
  <div >
        <?php 
      include("admin_login.php");
      ?>
</div>
<br>
      <div >
      <p class="sectionHead">Create New User</p>
      <div class="row">
        <div class="large-4 small-6 push-1 columns">
          <label  class="required">First Name
            <input type="text" name="fn" required id="fn" />
          </label>
          <small class="error">First Name cannot be blank!</small>
        </div>
        <div class="large-4 small-6 pull-2  columns">
          <label >Middle Name
            <input id="mn" name="mn" type="text" />
          </label>
          <small class="error"> Last Name cannot be blank!</small>
      </div>
      
      </div>
     <div class="row">
        <div class="large-4 small-6 push-1 columns">
          <label  class="required">Last Name
            <input id="ln" name="ln" required type="text" />
          </label>
          <small class="error"> Last Name cannot be blank!</small>
        </div>
      </div>
      <div class="row">
        <div class="large-4 small-4 push-1 columns">
          <label  class="required">Username <input type="text" name="uname" required id="uname" /> </label>
          <small class="error">Username cannot be blank!</small>  
        </div>
        <div class="large-3 small-3 push-1  columns">
        <br>
        <button class="tiny radius" type="button" id="availability">Check Availability</button>
        </div>
        <div  class="large-4 small-4 columns">
        <br> 

        <span id= "avlbl" class="availability"></span>
        </div>
      </div>
      <div class="row">
        <div class="large-4 small-6 push-1 columns">
          <label  class="required">Email Domain
            <select id="domain" name="domain" required >
            <?php
            require_once("core/util.php");
            $domains = getConfig("mail.domain");
            foreach($domains as $domain){
              echo "<option  value='$domain'>$domain</option>";
            }
            ?>
            </select>
          </label>
          <small class="error"> Domain cannot be blank!</small>
        </div>
      </div>
      <div class="row">
        <div class="large-6 small-8 push-1 columns">
        <label class="required"> PhoneNumber</label>
          <div class="row collapse">
            <div class="large-3 small-4 columns">
              <span class="prefix"><select name="phone" id="phone">
              <option value="home" selected="selected">Home</option>
              <option value="mobile">Mobile</option>
              </select></span>
            </div>
            <div class="large-9 small-8 columns">
              <input type="number" required  name="ph" id="ph" />
            </div>
        </div>
      </div>
      </div>
     <!-- <input id="confirm" type="hidden" name="confirm" value="false"> -->
      <div class="row">
        <div class="large-4 small-6 push-1 columns">
          <input type="submit" value="Create New User" class="button radius mybutton"/>
        </div>
        <div class="large-2 small-2 pull-6 columns">
          <img src="img/loading.gif" id="loading" style="display:none;"/>
        </div>
      </div>
      </div>
      <div class="clear"></div>
</form>
</div>
