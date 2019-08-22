<?php
$user_data = $this->session->userdata('userdata');
?>
<nav class="navbar navbar-expand navbar-dark bg-primary static-top" >

    <a class="navbar-brand mr-1" href="<?php echo base_url('dashboard'); ?>"><img src="<?php echo base_url('assets/img/mobiki_logo.svg'); ?>" style="width: 150px; margin-top: -80px; margin-bottom: -55px;" /></a>

    <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" onclick="openNav()" href="#">
        <i class="fas fa-bars"></i>
    </button>
    
    <a class="navbar-brand ml-10" href="#"> <?php //echo @$pageName; ?></a>



    <!-- Navbar -->


        <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="<?php echo base_url('dashboard') ?>">Dashboard <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Link </a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Dropdown
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="#">Action</a>
          <a class="dropdown-item" href="#">Another action</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#">Something else here</a>
        </div>
      </li>
    </ul>
  </div>

    <ul class="navbar-nav ml-auto ml-md-0">






        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-user-circle fa-fw" style="display: inline; "> <?php echo $user_data['name']; ?></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">User Settings</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a>
            </div>
        </li>
    </ul>

</nav> 
<script>
function openNav(){ 
  $('.sidebar').removeClass('sidebar navbar-nav toggled').addClass('sidebar navbar-nav');
};
</script>