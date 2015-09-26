{include file="_header.tpl"}
<!-- Custom CSS -->
<link href="{$site_root_path}assets/css/style.css" rel='stylesheet' type='text/css' />
<link href="{$site_root_path}assets/css/font-awesome.css" rel="stylesheet"> 
<!-- jQuery -->
<script src="{$site_root_path}assets/js/jquery.min.js"></script>
<!----webfonts--->
<link href='http://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900' rel='stylesheet' type='text/css'>
<!---//webfonts--->  
<!-- Bootstrap Core JavaScript -->
<script src="{$site_root_path}assets/js/bootstrap.min.js"></script>
</head>
<body id="login">
  <div class="login-logo">
    <a href="{$site_root_path}"><img src="{$site_root_path}assets/images/logo.png" alt=""/></a>
  </div>
{if $success}
     <h2 class="form-heading">Hello {$first_name}! You have been registered successfully</h2>
           <div class="registration">
          ALogin Here.
          <a class="" href="{$site_root_path}session/login.php">
              Login
          </a>
      </div>  
{else}
  <h2 class="form-heading">Register</h2>
  <form class="form-signin app-cam" action="{$site_root_path}session/register.php" method="post" >
      <p>Enter your personal details below</p>
      <input type="text" name="first_name" class="form-control1" placeholder="First Name" autofocus="">
      <input type="text" name="last_name" class="form-control1" placeholder="Last Name" autofocus="">      
      <input type="text" name="email" class="form-control1" placeholder="Email" autofocus="">
      <input type="password" name="pwd" class="form-control1" placeholder="Password">
      <input type="password" name="cpwd" class="form-control1" placeholder="Re-type Password">
      <div class="form-group">
        <label for="exampleInputFile">File input</label>
        <input type="file" id="exampleInputFile">
        <p class="help-block">Example block-level help text here.</p>
      </div>
      <label class="checkbox-custom check-success">
          <input type="checkbox" value="agree this condition" id="checkbox1"> <label for="checkbox1">I agree to the Terms of Service and Privacy Policy</label>
      </label>
      <button class="btn btn-lg btn-success1 btn-block" type="submit" value"Register">Submit</button>
      <div class="registration">
          Already Registered.
          <a class="" href="{$site_root_path}session/login.php">
              Login
          </a>
      </div>
  </form>
  {/if}
   <div class="copy_layout login register">
      <p>Copyright &copy; 2015 Modern. All Rights Reserved | Design by <a href="{$site_root_path}" target="_blank">EFC</a> </p>
   </div>
</body>
</html>
