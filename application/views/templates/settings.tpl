<div class="container">

  <div class="page-header">
    <h3>{$page_name} <small></small></h3>
  </div>

  <div class="row">

        <div class="col-xs-3">
      
          {if $userAlert != ''}
            <div class="alert alert-{$userAlertType}" role="alert">
              {$userAlert}
            </div>
          {/if}
            <!-- required for floating -->
            <!-- Nav tabs -->
            <ul class="nav nav-tabs tabs-left nav-stacked">
                <li class="active"><a href="#general" data-toggle="tab">General settings</a></li>
                <li><a href="#users" data-toggle="tab">Users</a></li>
            </ul>
        </div>
        <div class="col-xs-8">
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active" id="general"> <h4>General settings</h4> </div>

                <!-- Users tab -->
                <div class="tab-pane" id="users"> 
                  <h4>Users</h4>

                  <div class="table-responsive">
                    <table class="table table-condensed table-striped">
                      <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Action</th>
                      </tr>
                      {foreach from=$users item=user}
                      <tr>
                        <td>{$user.username}</td>
                        <td>{$user.email}</td>
                        <td></td>
                      </tr>
                      {/foreach}
                    </table>
                  </div> <!-- end div class=table... -->

                <hr>

                <h4>Add user</h4>

                <form action="index.php?page=settings" method="post" data-toggle="validator">
                  <label for="inputUsername">Username</label>
                  <input name="username"  type="text" id="inputUsername" class="form-control" placeholder="Username" aria-describedby="username_helpblock" required>

                  <label for="inputEmail">Email</label>
                  <input name="email" value="{$email}" type="email" id="inputEmail" class="form-control" placeholder="Email address" data-error="Invalid email address"required>
                  <div class="help-block with-errors"></div>

                  <label for="password">Password</label>
                  <input name="password" type="password" id="inputpassword" class="form-control" placeholder="New password" data-minlength="6" required>
                  <div class="help-block">Password must be at least 6 characters</div>                  
        
                  <input type="hidden" name="action" value="createuser">
                  
                  </br>
        
                  <button class="btn btn-sm btn-primary pull-right" type="submit">Create</button>
                </form>

                </div> <!-- end div class=tab-pane -->

        </div> <!-- end div class="tab-content -->

      <div class="clearfix"></div>
    </div> <!-- div class=col... -->

  </div> <!-- end div class=row -->

</div> <!-- end div class=container -->
