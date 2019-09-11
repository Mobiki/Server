



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
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>




<script>
    setInterval(function() {
        try {
            $("#alert_opened").load("dashboard/alert");
            $("#alert_suspended").load("dashboard/get_all_suspended_alerts");
            $("#alert_closed").load("dashboard/get_all_closed_alerts");
        } catch (err) {
            console.log("error");
        }
    }, 10000);


</script>