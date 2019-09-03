<?php $this->load->view('layout/up') ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><button type="button" id="btn_add_department" data-toggle='modal' data-target='#addDepartmentModal' class="btn btn-primary btn-sm">Add Department</button></h6>
            </div>
            <div class="card-body">
                <link rel="stylesheet" href="">

                <table id="dataTable" class="table table-striped table-bordered table-hover" style="width: 100%;">
                    <thead>
                        <tr>
                            <td>Name</td>
                            <td>Expiry Date</td>
                            <td style="width: 75px;">Edit</td>
                        </tr>
                    </thead>
                    <tbody id="departments">
                        <?php
                        foreach (@$departments as $key => $value) {

                            if ($value["expiry_date"]=="0000-00-00") {
                                $expiry_date = "";
                            } else {
                                $expiry_date = $value["expiry_date"];
                            }
                            
                            echo "<td>" . @$value["name"] . "</td>";
                            echo "<td>" . @$expiry_date . "</td>";

                            echo "<td>" . "<button type='button' 
                                data-toggle='modal' 
                                data-target='#addDepartmentModal' 
                                data-id='" . @$value["id"] . "' 
                                data-name='" . @$value["name"] . "' 
                                data-expiry_date='" . @$expiry_date . "' 
                            class='btn btn-success btn-sm'>Edit</button>" . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>


<div class="modal fade bd-modal-lg" tabindex="-1" role="dialog" id="addDepartmentModal" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title">Add Department</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_edit" action="departments/add" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                    <input type="hidden" id="eid" name="id" value="" />
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="ename" name="name" placeholder="" value="" required />
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label for="expiry_date">Expiry Date</label>
                                <input type="date" class="form-control" id="eexpiry_date" name="expiry_date" placeholder="" value="" />
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group" style="text-align: right;">
                                <button type="submit" class="btn btn-primary" id="btn_department_add">Add Department</button>

                            </div>
                        </div>
                    </div>
                </form>

                <div class="row" id="delet_department" style="display:none;">
                    <div class="col-12">
                        <form action="departments/delete" method="post">
                            <input type="hidden" id="did" name="id" value="" />
                            <button type="submit" class="btn btn-danger">Delete Department</button>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>



<?php $this->load->view('layout/down') ?>



<script>
    $('.btn-success').each(function() {
        var $this = $(this);
        $this.on("click", function() {
            $('#form_edit').attr('action', 'departments/edit');
            $('#modal_title').html("Edit Department");
            $('#btn_department_add').html("Edit Department");

            $('#eid').val($(this).data('id'));
            $('#ename').val($(this).data('name'));
            $('#eexpiry_date').val($(this).data('expiry_date'));

            $('#did').val($(this).data('id'));
            $('#cid').val($(this).data('id'));

            $('#delet_department').show();
            $('#password_section').hide();
        });
    });

    $('#btn_add_department').on("click", function() {
        $('#modal_title').html("Add Department");
        $('#btn_department_add').html("Add Department");
        $('#form_edit').attr('action', 'departments/add');
        $('#eid').val("");
        $('#ename').val("");
        $('#eexpiry_date').val("");
        $('#eid').val("");
        $('#cid').val("");
        $('#delet_department').hide();
    });

    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>