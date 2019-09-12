<ul class="sidebar navbar-nav " style="background-color: #007bff; ">
    <li class="nav-item">
        <a class="nav-link" style="padding-bottom: 0px;" href="<?php echo base_url('dashboard'); ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" style="padding-bottom: 0px;" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-fw fa-tablet"></i>
            <span>Devices</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <a class="dropdown-item" href="<?php echo base_url('devices'); ?>">Devices</a>
            <a class="dropdown-item" href="<?php echo base_url('gateways'); ?>">Gateways</a>
        </div>
    </li>
    <!--<li class="nav-item">
        <a class="nav-link" style="padding-bottom: 0px;" href="<?php //echo base_url('livemap'); 
                                                                ?>"><i class="fas fa-fw fa-map"></i></a>
    </li>-->
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" style="padding-bottom: 0px;" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-fw fa-building"></i>
            <span>Company's</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <a class="dropdown-item" href="<?php echo base_url('zones'); ?>">Zones</a>
            <hr>
            <a class="dropdown-item" href="<?php echo base_url('personnel'); ?>">Personnel</a>
            <a class="dropdown-item" href="<?php echo base_url('assets/index'); ?>">Assets</a>
            <a class="dropdown-item" href="<?php echo base_url('departments'); ?>">Departments</a>
        </div>
    </li>


    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" style="padding-bottom: 0px;" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-fw fa-bell"></i>
            <span>Alert</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <a class="dropdown-item" href="<?php echo base_url('alert/alerts'); ?>">Alerts</a>
            <a class="dropdown-item" href="<?php echo base_url('alert'); ?>">Alert Rules</a>
        </div>
    </li>

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" style="padding-bottom: 0px;" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-fw fa-bars"></i>
            <span>Logs</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <h6 class="dropdown-header">Companiey's Losg</h6>
            <a class="dropdown-item" href="<?php echo base_url('personnel_logs'); ?>">Personnel Logs/History</a>
            <a class="dropdown-item" href="<?php echo base_url('assets_logs'); ?>">Assets Logs</a>
            <h6 class="dropdown-header">Device Logs</h6>
            <a class="dropdown-item" href="<?php echo base_url('gateway_logs'); ?>">Gateway Logs</a>
            <a class="dropdown-item" href="<?php echo base_url('device_logs'); ?>">Device Logs</a>
            <h6 class="dropdown-header">Alert Logs</h6>
            <a class="dropdown-item" href="<?php echo base_url('alert/logs'); ?>">Alert Logs</a>
        </div>
    </li>
</ul>