<?php $this->load->view('layout/up') ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><button type="button" data-toggle='modal' data-target='#addZoneModal' class="btn btn-primary btn-sm">Add Zone</button></h6>
            </div>
            <div class="card-body">



                <?php

                foreach (@$zones as $key => $value) {
                    if ($value["parent_id"] == 0) {
                        echo '<div class="row">';
                        echo '<div class="col-2">';
                        echo $value["name"];
                        echo '</div>';
                        echo '<div class="col-1">';
                        echo '<button type="button" id="btn_zone_delete" data-id="" class="btn btn-outline-secondary btn-sm"><i class="fa fa-window-close" aria-hidden="true"></i></button>';
                        echo '</div>';
                        echo '<div class="col-1">';
                        echo '<button type="button" id="btn_zone_delete" data-id="" class="btn btn-outline-danger btn-sm"><i class="fa fa-window-close" aria-hidden="true"></i></button>';
                        echo '</div>';
                        echo '</div>';
                        hierarchical(1,$zones, $value["id"]);
                        echo " <hr>";
                    }
                }

                function hierarchical($a,$zones, $id)
                {
                    foreach ($zones as $key => $value) {
                        if ($value["parent_id"] == $id) {
                            echo '<div class="row">';
                            for ($i = 0; $i < $a; $i++) {
                                echo '&nbsp;-';
                                echo $a;
                            }
                            echo '<div class="col-2">';
                            echo $value["name"];
                            echo '</div>';
                            echo '</div>';
                            $a = $a + 1; 
                            
                            hierarchical($a,$zones, $value["id"]);
                        }
                        
                    }
                }
                ?>


                <hr>
                <br>
                <br>
                <?php
                //print_r($zones);
                $zz = $zones;
                $zzz = $zones;
                $zzzz = $zones;
                foreach ($zones as $key => $zvalue) {
                    if ($zvalue["parent_id"] == 0) {
                        echo $zvalue["name"];
                        echo  "<br>";
                        foreach ($zz as $key => $zzvalue) {

                            if ($zzvalue["parent_id"] == $zvalue["id"]) {
                                echo "└--- " . $zzvalue["name"];
                                echo  "<br>";
                                foreach ($zzz as $key => $zzzvalue) {
                                    if ($zzzvalue["parent_id"] == $zzvalue["id"]) {
                                        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└--- " . $zzzvalue["name"];
                                        echo "<br>";


                                        foreach ($zzzz as $key => $zzzzvalue) {
                                            if ($zzzzvalue["parent_id"] == $zzzvalue["id"]) {
                                                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└--- " . $zzzzvalue["name"];
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




            </div>
        </div>
    </div>
</div>

<div id="dv"></div>


<div class="modal fade bd-modal-lg" id="addZoneModal" tabindex="-1" role="dialog" aria-labelledby="addZoneModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Add Zone</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" id="addmodalbody">
                <form action="zones/add" method="post">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">

                                <label for="name">Zone name</label>
                                <input type="zonename" class="form-control" id="name" name="name" placeholder="">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="parent_id">Parent Zone</label>
                                <select class="form-control" id="parent_id" name="parent_id">
                                    <option value="0">Null</option>
                                    <?php foreach ($zones as $key => $zvalue) {  ?>
                                        <option value="<?php echo $zvalue["id"]; ?>"><?php echo $zvalue["name"]; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <input type="zonename" class="form-control" id="description" name="description" placeholder="">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Add Zone</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php $this->load->view('layout/down') ?>