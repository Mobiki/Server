<?php $this->load->view('layout/up') ?>






<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3">
                    <?php
                    $currentDate =  time();
                    date("Y-m-d", $currentDate)
                    ?>
                    <h6 class="m-0 font-weight-bold text-primary"><?php //echo date("d/m/Y", $currentDate); 
                                                                    ?></h6>
                </div>
                <div class="card-body">
                    <section class="example">
                        <div class="table-responsive" id="datatable">
                            <link rel="stylesheet" href="">


                            <table id="dataTable" class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </thead>
                                <tbody id="log">
                                    <?php
                                    foreach ($userlog as $userlogkey => $value) {
                                        $ulogjson = json_decode($userlogkey, true);
                                        echo "<tr>";
                                            echo "<td>" . date("d-m-Y H:i:s", $value) . "</td>";
                                            echo "<td>" . $ulogjson["gateway"] . "</td>";
                                            echo "<td>" . $ulogjson["x"] . "</td>";
                                            echo "<td>" . $ulogjson["y"] . "</td>";
                                            echo "<td>" . $ulogjson["z"] . "</td>";
                                            echo "<td>" . $ulogjson["rssi"] . "</td>";
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

</div>


<?php


//print_r($userlog);


/*foreach ($userlog as $userlogkey => $value) {
    echo $userlogkey . " / / / " . $value;
    echo "<br>";
}*/

?>






<?php $this->load->view('layout/down') ?>