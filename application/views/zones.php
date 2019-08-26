<?php $this->load->view('layout/up') ?>




<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header py-3"> 
                        <?php
                        ?>
                        <h6 class="m-0 font-weight-bold text-primary"><button type="button" data-toggle='modal' data-target='#addGatewayModal' class="btn btn-primary btn-sm">Add Zone</button> - <button type="button" class="btn btn-secondary btn-sm" onclick="sendtoredis()">Update</button> </h6>
                    </div>
                    <div class="card-body">
                        <section class="example">
                            <div class="table-responsive" id="datatable">
                                <link rel="stylesheet" href="">


                                <table id="dataTable" class="table table-striped table-bordered table-hover">
                                    
                                    <tbody id="gateways">

                                        <?php 
                                        
                                        //print_r($zones);
                                        $zz = $zones;
                                        $zzz = $zones;
                                        $zzzz = $zones;
                                        foreach ($zones as $key => $zvalue) {
                                            if ($zvalue["parent_id"] == 0) {
                                                echo $zvalue["name"] ;
                                                echo  "<br>";

                                                foreach ($zz as $key => $zzvalue) {
                                                    if ($zzvalue["parent_id"] == $zvalue["id"]) {
                                                        echo "└--- ".$zzvalue["name"];
                                                        echo  "<br>";

                                                        foreach ($zzz as $key => $zzzvalue) {
                                                            if ($zzzvalue["parent_id"] == $zzvalue["id"]) {
                                                                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└--- ".$zzzvalue["name"];
                                                                echo "<br>";


                                                                foreach ($zzzz as $key => $zzzzvalue) {
                                                                    if ($zzzzvalue["parent_id"] == $zzzvalue["id"]) {
                                                                        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└--- ".$zzzzvalue["name"];
                                                                        echo "<br>";
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        ?>


                                    </tbody>
                                </table>

                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>

<div id="dv"></div>

<?php $this->load->view('layout/down') ?>


<script>
    function sendtoredis(){
        $("#dv").load("zones/toredis");
    }
</script>