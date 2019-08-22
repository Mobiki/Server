<!DOCTYPE html>
<html lang="en">
<?php $this->load->view("layout/head"); ?>

<link href="<?php echo base_url("assets/css/bootstrap-datepicker.min.css"); ?>" rel="stylesheet">
<style>

</style>

<body id="page-top">
    <!-- Navbar -->
    <?php $this->load->view('layout/navbar') ?>
    <div id="wrapper">
        <!-- Sidebar -->
        <?php $this->load->view('layout/sidebar') ?>
        <div id="content-wrapper">
            <div class="container-fluid">
                <?php
                $user_companies = $this->session->userdata('user_companies');
                ?>
                <div class="col">
                    <div class="row">
                        <div class="col-2">
                            <p class="font-weight-bold">Gösterilen Firma : </p>
                        </div>
                        <div class="col-3"><select class="browser-default custom-select" id="companies">
                                <option value="0">Select Companies</option>
                                <?php
                                foreach ($user_companies as $user_companie) {
                                    ?><option value="<?php echo $user_companie['id']; ?>"><?php echo $user_companie['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-2">
                            <p class="font-weight-bold">Cihaz : </p>
                        </div>
                        <div class="col-3"><select class="browser-default custom-select" id="sensors">
                            </select>
                        </div>
                    </div><br>
                    <div class="row">

                        <div class="col-2">
                            <p class="font-weight-bold">Başlangıç Tarihi : </p>
                        </div>
                        <div class="col-3"><input id="start" name="start" class="date-pick form-control" value="<?php echo date("m/d/Y"); ?>" data-date-format="mm/dd/yyyy" type="text"></div>
                        <div class="col-2">
                            <p class="font-weight-bold">Bitiş Tarihi : </p>
                        </div>
                        <div class="col-3"><input id="finish" name="finish" class="date-pick form-control" value="<?php echo date("m/d/Y"); ?>" data-date-format="mm/dd/yyyy" type="text" /></div>
                        <div class="col-1"><button id="gerReport" class="btn btn-success">Raporla</button></div>
                        <div class="col-1"><img height="40px" src="https://de.marketscreener.com/images/loading_100.gif" style="display: none;" id="imgProgress1" /></div>

                    </div>
                </div>
                <hr>
                <div class="col">
                    <div class="row" id="report">


                    </div>
                </div>



            </div>
            <!-- /.container-fluid -->
            <!-- Sticky Footer -->
        </div>
        <!-- /.content-wrapper -->
    </div>
    <!-- /#wrapper -->

    <!-- Scroll to Top Button // Logout Modal-->
    <?php $this->load->view("layout/logoutmodal"); ?>
    <!-- JavaScript-->
    <?php $this->load->view("layout/scripts"); ?>
    <script src="<?php echo base_url("assets/js/moment-with-locales.js"); ?>"></script>
    <script src="<?php echo base_url("assets/js/bootstrap-datepicker.min.js"); ?>"></script>

</body>

<script>
    $('.datepicker').datepicker({
        format: 'mm/dd/yyyy'
    });

    $('#gerReport').on('click', function(event) {
        $('#report').empty();
        var startDate = $('#start').val().split('/');
        var startepoch = new Date(startDate[2], startDate[0] - 1, startDate[1], 00, 00, 00).getTime() / 1000;
        var finishDate = $('#finish').val().split('/');
        var finishepoch = new Date(finishDate[2], finishDate[0] - 1, finishDate[1], 23, 59, 59).getTime() / 1000;
        var sensorsmac = $('#sensors').val();

        //alert(startepoch + '  ' + finishepoch+' '+sensorsmac);
        if (sensorsmac != null) {
            $("#imgProgress1").show();
            $("#report").load("reports/getMacReports?sensor=" + sensorsmac + "&sDate=" + startepoch + "&fDate=" + finishepoch, function() {
                $("#imgProgress1").hide();
                console.log("İlk yükleme 2");
            });
        } else {
            alert('Select device');
        }
    });


    $('#companies').on('change', function() {

        //get companies sensors from companies id

        $.get("sensor/getbycompanies?cid=" + $(this).val(), function(data) {

            //alert(data);
            $('#sensors').find('option')
                .remove()
                .end()
                .append(data);
        });


    });

    $('input.date-pick').datepicker().on('change', function(ev) {
        var firstDate = $(this).val();


        //alert(epoch);
    });


    // Call the dataTables jQuery plugin
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });

</script>

</html>