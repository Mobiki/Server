<?php $this->load->view('layout/up') ?>

    <!-- All-->

    <?php

    if ($section == 'all') { ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header py-3">
                        <?php
                        ?>
                        <h6 class="m-0 font-weight-bold text-primary"><button type="button" data-toggle='modal' data-target='#addGatewayModal' class="btn btn-primary btn-sm">Add Gateway</button> - <button type="button" class="btn btn-secondary btn-sm" onclick="sendtoredis()">Update</button> </h6>
                    </div>
                    <div class="card-body">
                        <section class="example">
                            <div class="table-responsive" id="datatable">
                                <link rel="stylesheet" href="">


                                <table id="dataTable" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <td>Serial No</td>
                                            <td>Gateway Name</td>
                                            <td>Location</td>
                                            <td>Description</td>
                                            <td>Zone Name</td>
                                            <td>Edit</td>
                                        </tr>
                                    </thead>
                                    <tbody id="gateways">

                                        <?php /*print_r($zones);
                                        $zz = $zones;
                                        $zzz = $zones;
                                        $zzzz = $zones;
                                        echo  "<br>";
                                        echo  "<br>";
                                        foreach ($zones as $key => $zvalue) {
                                            if ($zvalue["parent_id"] == 0) {
                                                echo $zvalue["name"] . " " . $zvalue["id"];
                                                echo  "<br>";

                                                foreach ($zz as $key => $zzvalue) {
                                                    if ($zzvalue["parent_id"] == $zvalue["id"]) {
                                                        echo $zzvalue["name"] . "   " . $zzvalue["id"];
                                                        echo "<hr>";

                                                        foreach ($zzz as $key => $zzzvalue) {
                                                            if ($zzzvalue["parent_id"] == $zzvalue["id"]) {
                                                                echo $zzzvalue["name"] . "   " . $zzzvalue["id"];
                                                                echo "<hr>";


                                                                foreach ($zzzz as $key => $zzzzvalue) {
                                                                    if ($zzzzvalue["parent_id"] == $zzzvalue["id"]) {
                                                                        echo $zzzzvalue["name"] . "   " . $zzzzvalue["id"];
                                                                        echo "<hr>";
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }*/

                                        ?>
                                        <?php
                                        foreach ($gateways as $key => $value) {
                                            $zone_name = "";
                                            foreach ($zones as $key => $zvalue) {
                                                if ($zvalue["id"] == $value["zone_id"]) {
                                                    $zone_name = $zvalue["name"];
                                                }
                                            }

                                            echo "<tr>";
                                            echo "<td>" . $value["mac"] . "</td>";
                                            echo "<td>" . $value["name"] . "</td>";
                                            echo "<td>" . $value["lat"] . "," . $value["lng"] . "</td>";
                                            echo "<td>" . $value["description"] . "</td>";
                                            echo "<td>" . $zone_name . "</td>";


                                            echo "<td>" . "<button type='button' data-toggle='modal' data-target='#editGatewayModal' data-id='" . $value["id"] . "' data-mac='" . $value["mac"] . "' data-name='" . $value["name"] . "' data-lat='" . $value["lat"] . "' data-lng='" . $value["lng"] . "'data-description='" . $value["description"] . "' class='btn btn-success'>Edit</button>" . "</td>";
                                            echo "</tr>";
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


    <?php } ?>

    <!-- detail-->



<div class="modal fade bd-example-modal-lg" id="addGatewayModal" tabindex="-1" role="dialog" aria-labelledby="addGatewayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Add Gateway</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" id="addmodalbody">
                <form action="gateways/add" method="post">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">

                                <label for="mac">Gateway SN</label>
                                <input type="gatewaysn" class="form-control" id="mac" name="mac" placeholder="">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="name">Gateway Name</label>
                                <input type="gatewaysn" class="form-control" id="name" name="name" placeholder="">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="lat">Latitude</label>
                                <input type="gatewaysn" class="form-control" id="lat" name="lat" placeholder="">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="lng">Longitude</label>
                                <input type="gatewaysn" class="form-control" id="lng" name="lng" placeholder="">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <input type="gatewaysn" class="form-control" id="description" name="description" placeholder="">
                            </div>
                        </div>
                        <div class="col-6">

                            <div class="form-group">
                                <label for="zone_id">Zone</label>
                                <select class="form-control" id="zone_id" name="zone_id">
                                    <?php foreach ($zones as $key => $zvalue) {  ?>
                                        <option value="<?php echo $zvalue["id"]; ?>"><?php echo $zvalue["name"]; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Add Gateway</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade bd-example-modal-lg" id="editGatewayModal" tabindex="-1" role="dialog" aria-labelledby="editGatewayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Edit Gateway</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" id="editmodalbody">
                <form action="gateways/edit" method="post">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <input type="hidden" id="eid" name="eid">
                                <label for="mac">Gateway SN</label>
                                <input type="text" class="form-control" id="emac" name="emac">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="name">Gateway Name</label>
                                <input type="text" class="form-control" id="ename" name="ename">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="lat">Latitude</label>
                                <input type="text" class="form-control" id="elat" name="elat">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="lng">Longitude</label>
                                <input type="text" class="form-control" id="elng" name="elng">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <input type="text" class="form-control" id="edescription" name="edescription">
                            </div>
                        </div>
                        <div class="col-6">

                            <div class="form-group">
                                <label for="zone_id">Zone</label>
                                <select class="form-control" id="ezone_id" name="ezone_id">
                                    <?php foreach ($zones as $key => $zvalue) {  ?>
                                        <option value="<?php echo $zvalue["id"]; ?>"><?php echo $zvalue["name"]; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-info">Edit Gateway</button>
                </form>

                <hr>

                <div class="row">
                    <div class="col-6">
                        <form action="gateways/delete" method="post">
                            <input type="hidden" id="hmac" name="hmac" value="" />
                            <input type="hidden" id="hid" name="hid" value="" />
                            <button type="submit" class="btn btn-primary">Delete Gateway</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="gw"></div>

<?php $this->load->view('layout/down') ?>
<script>
    $('.btn-success').each(function() {
        var $this = $(this);

        $this.on("click", function() {
            $('#hid').val($(this).data('id'));
            $('#hmac').val($(this).data('mac'));

            $('#eid').val($(this).data('id'));
            $('#emac').val($(this).data('mac'));
            $('#ename').val($(this).data('name'));
            $('#elat').val($(this).data('lat'));
            $('#elng').val($(this).data('lng'));
            $('#edescription').val($(this).data('description'));

            /*$.post("users/get/" + $(this).data('id'), function(data) {
                $("#editmodalbody").html(data).fadeIn();
            });*/
        });
    });

    function sendtoredis(){
        $("#gw").load("gateways/toredis");
    }
</script>