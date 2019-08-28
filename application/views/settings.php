<?php $this->load->view('layout/up') ?>
<div class="col-12">



    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="redis-tab" data-toggle="pill" href="#redis" role="tab" aria-controls="redis" aria-selected="true">Redis</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="firebase-tab" data-toggle="pill" href="#firebase" role="tab" aria-controls="firebase" aria-selected="false">Firebase</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="settings-tab" data-toggle="pill" href="#settings" role="tab" aria-controls="settings" aria-selected="false">Settings</a>
        </li>
    </ul>
    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="redis" role="tabpanel" aria-labelledby="redis-tab">
            <p>
                <h4>Redis settings</h4>
            </p>
            <br>
            <form method="post" action="settings/redisSetting">
                <div class="row">
                    <div class="col-1">Enable</div>
                    <div class="col-2" style="text-align: center;"><input class="form-check-input" type="checkbox" name="redis_status" <?php
                                                                                                                                        if ($this->config->item('redis_status') == true) {
                                                                                                                                            echo "checked";
                                                                                                                                        }
                                                                                                                                        if ($this->config->item('redis_status') == false) {
                                                                                                                                            echo "";
                                                                                                                                        }
                                                                                                                                        ?> /></div>
                </div>
                <br>
                <div class="row">
                    <div class="col-1">Host</div>
                    <div class="col-2"><input class="form-control" type="text" name="redis_host" value="<?php echo @$this->config->item('redis_host'); ?>" required /></div>
                </div>
                <br>
                <div class="row">
                    <div class="col-1">Port</div>
                    <div class="col-2"><input class="form-control" type="number" name="redis_port" value="<?php echo @$this->config->item('redis_port'); ?>" required /></div>
                </div>
                <br>
                <div class="row">
                    <div class="col-1">Auth</div>
                    <div class="col-2"><input class="form-control" type="text" name="redis_auth" value="<?php echo @$this->config->item('redis_auth'); ?>" /></div>
                </div>
                <br>
                <div class="row">
                    <div class="col-1">Scheme</div>
                    <div class="col-2"><input class="form-control" type="text" name="redis_scheme" value="<?php echo @$this->config->item('redis_scheme'); ?>" required /></div>
                </div>
                <br>
                <div class="row">
                    <div class="col-1" id="redistest"><button type="button" class="btn btn-primary btn-sm" onclick="redistest()">Test</button></div>
                    <div class="col-2" style="text-align: center;"><button type="submit" class="btn btn-primary btn-sm">Save</button></div>
                </div>

            </form>


        </div>
        <div class="tab-pane fade" id="firebase" role="tabpanel" aria-labelledby="firebase-tab">

            Firebase settings
        </div>
        <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">

            Settings
        </div>


    </div>
</div>




<?php $this->load->view('layout/down') ?>

<script>
    function redistest() {
        $("#redistest").load("settings/redisTest");
    }
</script>