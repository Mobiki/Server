<ul class="sidebar navbar-nav " style="background-color: #007bff; ">
    <li class="nav-item">
        <a class="nav-link" style="padding-bottom: 0px;" href="<?php echo base_url('dashboard') ?>">
        <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <!--<li class="nav-item">
        <a class="nav-link" style="padding-bottom: 0px;" href="<?php echo base_url('livemap') ?>"><i class="fas fa-fw fa-map"></i></a>
    </li>-->
    <li class="nav-item">
        <a class="nav-link" style="padding-bottom: 0px;" href="<?php echo base_url('zones') ?>">
        <i class="fas fa-fw fa-window-restore"></i>
            <span>Zones</span>
        </a>
    </li>
    
    <li class="nav-item dropdown" >
        <a class="nav-link dropdown-toggle" style="padding-bottom: 0px;" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-fw fa-tablet"></i>
          <span>Devices</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
          <h6 class="dropdown-header"> </h6>
          <a class="dropdown-item" href="<?php echo base_url('devices'); ?>">Devices</a>
          <a class="dropdown-item" href="<?php echo base_url('gateways'); ?>">Gateways</a>
          <a class="dropdown-item" href="<?php echo base_url('sensors'); ?>">Sensors</a>
        </div>
      </li>



    <li class="nav-item">
        <a class="nav-link" style="padding-bottom: 0px;" href="<?php echo base_url('personnel') ?>">
        <i class="fa fa-male" aria-hidden="true"></i>
            <span>Personnel</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" style="padding-bottom: 0px;" href="<?php echo base_url('assets/index') ?>">
        <i class="fa fa-th-large" aria-hidden="true"></i>
            <span>Assets</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" style="padding-bottom: 0px;" href="<?php echo base_url('departments') ?>">
        <i class="fa fa-object-ungroup" aria-hidden="true"></i>
            <span>Departments</span>
        </a>
    </li>  
    <!--<li class="nav-item">
        <a class="nav-link" style="padding-bottom: 0px;" href="<?php echo base_url('history') ?>">
            <span>Personnel History</span>
        </a>
    </li>-->
    <li class="nav-item">
        <a class="nav-link" style="padding-bottom: 0px;" href="<?php echo base_url('alert') ?>">
        <i class="fas fa-fw fa-bell"></i>
            <span>Alert</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" style="padding-bottom: 0px;" href="<?php echo base_url('users') ?>">
        <i class="fas fa-fw fa-users"></i>
            <span>Users</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" style="padding-bottom: 0px;" href="<?php echo base_url('settings') ?>">
        <i class="fa fa-cogs" aria-hidden="true"></i>
            <span>Settings</span>
        </a>
    </li>
</ul>