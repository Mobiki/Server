<div class="col-lg-3 col-md-6 col-sm-12" id="alert_widget_id">
    <div class="card card-stats">
        <div class="card-body" id="alert_body">
            <a href="<?php echo base_url("alert/alerts"); ?>">Alerts</a></div>
    </div>
    <div class="card-footer" id="alert_widget_footer" style="padding: 0px;" style="display: block;">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div style="font-size: small;">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead style="text-align: center;">
                                <tr style="background-color: #ff6961;">
                                    <th style='padding: 7px;'>Alert</th>
                                    <th style='padding: 7px;'>Time</th>
                                    <th style='padding: 7px;'>Name</th>
                                    <th style='padding: 7px;'>Location</th>
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
            </div>
        </div>
    </div>
</div>

<script>
    setInterval(function() {
        try {
            $("#alert_opened").load("<?php echo base_url('alert/get_all_open_alerts?pageID=1'); ?>");
        } catch (err) {
            console.log("error");
        }
    }, 10000);
</script>