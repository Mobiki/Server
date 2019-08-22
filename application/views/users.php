<?php $this->load->view('layout/up'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addUser">Add User</button>
            </div>
            <div class="card-body">
                <table id="users" class="table table-striped table-bordered table-hover" style="width:100%">
                    <thead>
                        <!--`id``role_id``name``email``password``phone``description``token`-->
                        <tr>
                            <td>#</td>
                            <td>Name</td>
                            <td>Email</td>
                            <td>Phone</td>
                            <td>Description</td>
                            <td>Edit</td>
                        </tr>
                    </thead>
                    <tbody id="userstbody">
                        <?php foreach (@$users_data as $key => $value) { ?>
                        <tr>
                            <td><?php echo @$value["id"]; ?></td>
                            <td><?php echo @$value["name"]; ?></td>
                            <td><?php echo @$value["email"]; ?></td>
                            <td><?php echo @$value["phone"]; ?></td>
                            <td><?php echo @$value["description"]; ?></td>
                            <td><button type="button" class="btn btn-warning btn-sm">Edit</button></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" id="addUser" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title" id="myLargeModalLabel">Add User</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
    </div>
  </div>
</div>

<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" id="editUser" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title" id="myLargeModalLabel">Edit User</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
    </div>
  </div>
</div>


<?php $this->load->view('layout/down'); ?>

<script>
    $(document).ready(function() {
        $('#users').DataTable();
    });
</script>