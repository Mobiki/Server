<?php $this->load->view('layout/up') ?>

<div class="row">
    <div class="col-12" id="alert_widget_id">
        <div class="card card-stats">
        </div>
        <div class="card-footer" id="alert_widget_footer" style="padding: 0px;" style="display: block;">

            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <div style="font-size: small;">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead style="text-align: center;">
                                    <tr style="background-color: #ff6961;">
                                        <th style='padding: 7px;'>Alert</th>
                                        <th style='padding: 7px;'>Time</th>
                                        <th style='padding: 7px;'>Name</th>
                                        <th style='padding: 7px;'>Location</th>
                                        <th style='padding: 7px;'>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="alert_opened">
                                    <tr>
                                        <td colspan="5">Loading
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div style="font-size: small;">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead style="text-align: center;">
                                    <tr style="background-color: #fdfd96;">
                                        <th style='padding: 7px;'>Alert</th>
                                        <th style='padding: 7px;'>Time</th>
                                        <th style='padding: 7px;'>Name</th>
                                        <th style='padding: 7px;'>Location</th>
                                        <th style='padding: 7px;'>User Name</th>
                                        <th style='padding: 7px;'>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="alert_suspended">
                                    <tr>
                                        <td colspan="6">Loading
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div style="font-size: small;">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead style="text-align: center;">
                                    <tr style="background-color: #77dd77;">
                                        <th style='padding: 7px;'>Alert</th>
                                        <th style='padding: 7px;'>Time</th>
                                        <th style='padding: 7px;'>Name</th>
                                        <th style='padding: 7px;'>Location</th>
                                        <th style='padding: 7px;'>User Name</th>
                                        <th style='padding: 7px;'>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="alert_closed">
                                    <tr>
                                        <td colspan="6">Loading
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layout/down') ?>

<script>
    setInterval(function() {
        try {
            $("#alert_opened").load("<?php echo base_url('alert/get_all_open_alerts'); ?>");
            $("#alert_suspended").load("<?php echo base_url('alert/get_all_suspended_alerts'); ?>");
            $("#alert_closed").load("<?php echo base_url('alert/get_all_closed_alerts'); ?>");
        } catch (err) {
            console.log("error");
        }
    }, 10000);




    $(document).ready(function() {
        $('#closed_alerts').DataTable({
            destroy: true,
            "processing": true,
            "order": [
                [2, "asc"]
            ],
            "ajax": {
                "url": "<?php echo base_url("alert/get_logs?person_id="); ?>" + $('#eid').val(),
                dataSrc: ''
            },
            "columns": [{
                    "data": "gateway_name"
                }, {
                    "data": "date_time"
                    /*"render": function(data, type, now) {
                        var timestamp = data;
                        var date = new Date(timestamp * 1000);
                        var year = date.getFullYear();
                        var month = date.getMonth() + 1;
                        var day = date.getDate();
                        var hours = date.getHours();
                        var minutes = date.getMinutes();
                        var seconds = date.getSeconds();
                        return year + "/" + month + "/" + day + " " + hours + ":" + minutes + ":" + seconds;
                    }*/
                },
                {
                    "data": "epoch"
                },
                {
                    "data": "status"
                }
            ],
            "columnDefs": [{
                "targets": [2],
                "visible": false
            }]
        });
    });
</script>