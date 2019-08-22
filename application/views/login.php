<?php
$flashdata = $this->session->flashdata();
$user_data = $this->session->userdata('userdata');
//print_r($user_data);
//print_r($flashdata);
if ($user_data['auth'] == '1') {
    redirect(site_url() . 'dashboard');
} else {
    //redirect(site_url() . 'login');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Mobiki</title>
    <?php $this->load->view("layout/styles"); ?>
</head>
<?php

?>

<body class="bg-dark">
    <div class="container">
        <div class="card card-login mx-auto mt-5">
            <div class="card-header">Login - Mobiki.in</div>
            <div class="card-body">
                <?php  ?>
                <form action="<?php echo base_url('login/auth') ?>" method="post" name="form">
                    <div class="form-group">
                        <div class="form-label-group">
                            <input type="email" id="inputEmail" class="form-control" name="email" placeholder="Email address" required="required" autofocus="autofocus">
                            <label for="inputEmail">Email address</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-label-group">
                            <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Password" required="required">
                            <label for="inputPassword">Password</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" value="remember-me" name="remember" id="remember">
                                Remember Password
                            </label>
                        </div>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary">Login</button>
                    <!--<a class="btn btn-primary btn-block" href="index.html">Login</a>-->
                </form>
                <?php echo @$validation_error; ?>
                <?php //echo md5('654321'. md5('mobiki')); 
                ?>
                <?php echo @$email; ?>
                <?php echo @$password; ?>
                <!--<div class="text-center">
          <a class="d-block small mt-3" href="register.html">Register an Account</a>
          <a class="d-block small" href="forgot-password.html">Forgot Password?</a>
        </div>-->
            </div>
        </div>
    </div>
    <?php $this->load->view("layout/scripts"); ?>
</body>

</html>