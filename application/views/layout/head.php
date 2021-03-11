<?php
//$flashdata = $this->session->flashdata();
$user_data = $this->session->userdata('userdata');
//print_r($user_data);
//print_r($flashdata);
if ($user_data['auth'] == 'auth1') { } else {
    redirect(site_url() . 'login');
}

?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="OpenMobiki">
    <meta name="author" content="Mobiki">
    <title><?php echo @$pageName; ?> - OpenMobiki</title>
    <?php $this->load->view("layout/styles"); ?>

    <style>
        .navbar {
            border-radius: 0px;
            margin-bottom: 0px;
        }
    </style>
</head>
<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
<script>
  var OneSignal = window.OneSignal || [];
  OneSignal.push(function() {
    OneSignal.init({
      appId: "66ca1742-6b2d-4e81-ab7f-6a5e41eae1ee",
    });
  });
</script>