<!DOCTYPE HTML>
<html>
<head>
<title>{$controller_title} | {$app_title} </title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Modern Responsive web template, Bootstrap Web Templates, Flat Web Templates, Andriod Compatible web template, 
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyErricsson, Motorola web design" />
{literal}
<script type="application/x-javascript"> 
    addEventListener("load", function() { 
      setTimeout(hideURLbar, 0); 
    }, false); 

    function hideURLbar(){ window.scrollTo(0,1); } </script>
{/literal}
 <!-- Bootstrap Core CSS -->
<link href="{$site_root_path}assets/css/bootstrap.min.css" rel='stylesheet' type='text/css' />
<!-- Custom CSS -->
<link href="{$site_root_path}assets/css/style.css" rel='stylesheet' type='text/css' />
<link href="{$site_root_path}assets/css/font-awesome.css" rel="stylesheet"> 
<!-- jQuery -->
<script src="{$site_root_path}assets/js/jquery.min.js"></script>
<!----webfonts--->

<!---//webfonts--->  
<!-- Bootstrap Core JavaScript -->
<script src="{$site_root_path}assets/js/bootstrap.min.js"></script>
</head>
<body id="login">
  <div class="login-logo">
    <a href="index.html"><h2>OLX Suggestion / Recommendation Engine</h2></a>
  </div>
  <h2 class="form-heading">login</h2>
  <div class="app-cam">
	  <form action="{$site_root_path}session/login.php" method="post">
    {literal}
    <!--    
		<input type="text" class="text" name="email" value="E-mail address" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'E-mail address';}">

		<input type="password" value="Password" name="pwd" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Password';}">
        {/literal}
		<div class="submit"><input type="submit" value="Login"></div>
   -->
		<div class="login-social-link">
          <a href="{$fb_login_url}" class="facebook">
              Facebook
          </a> Loing with FACEBOOK
        </div>
     
		<ul class="new">
			<li class="new_left"><p><a href="#">Forgot Password ?</a></p></li>
			<li class="new_right"><p class="sign">New here ?<a href="register.html"> Sign Up</a></p></li>
			<div class="clearfix"></div>
		</ul>
     
	</form>
  </div>
   <div class="copy_layout login">
      <p>Copyright &copy; 2015 Modern. All Rights Reserved | Design by <a href="http://localhost/EFC/webapp/" target="_blank">EFC</a> </p>
   </div>
</body>
</html>
