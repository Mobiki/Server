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
    <meta name="description" content="">
    <meta name="author" content="">
    <title><?php echo @$pageName; ?> - OpenMobiki</title>
    <?php $this->load->view("layout/styles"); ?>

    <style>
        .navbar {
            border-radius: 0px;
            margin-bottom: 0px;
        }
    </style>
</head>