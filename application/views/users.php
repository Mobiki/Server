<?php $this->load->view('layout/up'); ?>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header py-3">
        <button type="button" class="btn btn-primary btn-sm" id="btn_add_user" data-toggle="modal" data-target="#addUser">Add User</button>
      </div>
      <div class="card-body">
        <table id="users" class="table table-striped table-bordered table-hover" style="width:100%">
          <thead>
            <tr>
              <td>#</td>
              <td>Name</td>
              <td>Role</td>
              <td>Email</td>
              <td>Phone</td>
              <td>Description</td>
              <td>Edit</td>
            </tr>
          </thead>
          <tbody id="userstbody">
            <?php foreach (@$users_data as $key => $value) {
              if (@$value["role_id"] == 0) {
                $role_name = "";
              } else {
                foreach (@$users_role as $key => $dvalue) {
                  if ($dvalue["id"] == $value["role_id"]) {
                    $role_name = $dvalue["name"];
                    break;
                  } else {
                    $role_name = "";
                  }
                }
              }
              ?>
              <tr>
                <td><?php echo @$value["id"]; ?></td>
                <td><?php echo @$value["name"]; ?></td>
                <td><?php echo @$role_name; ?></td>
                <td><?php echo @$value["email"]; ?></td>
                <td><?php echo @$value["phone"]; ?></td>
                <td><?php echo @$value["description"]; ?></td>
                <td><button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addUser" data-id="<?php echo @$value["id"]; ?>" data-name="<?php echo @$value["name"]; ?>" data-role_id="<?php echo @$value["role_id"]; ?>" data-email="<?php echo @$value["email"]; ?>" data-phone="<?php echo @$value["phone"]; ?>" data-description="<?php echo @$value["description"]; ?>">Edit</button></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade bd-modal-lg" tabindex="-1" role="dialog" id="addUser" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="modal_title">Add User</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form_edit" action="users/add" method="post">
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
                <label for="role_id">User Role</label>
                <select class="form-control" id="erole_id" name="role_id">
                  <?php foreach (@$users_role as $key => $dtvalue) { ?>
                    <option value="<?php echo $dtvalue["id"]; ?>"><?php echo $dtvalue["name"]; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label for="name">Email</label>
                <input type="text" class="form-control" id="eemail" name="email" placeholder="" value="" required />
              </div>
            </div>
            <div class="col-6">
              <div class="form-group" id="password_section">
                <label for="name">Password</label>
                <input type="password" class="form-control" id="epassword" name="password" placeholder="" value="" />
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label for="name">Phone</label>
                <input type="tel" class="form-control" id="ephone" name="phone" placeholder="" value="" />
              </div>
            </div>
            <div class="col-6">
              <div class="form-group">
                <label for="name">Description</label>
                <input type="text" class="form-control" id="edescription" name="description" placeholder="" value="" />
              </div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-12">
              <div class="form-group" style="text-align: right;">
                <button type="submit" class="btn btn-primary" id="btn_user_add">Add User</button>
              </div>
            </div>
          </div>
        </form>
        <hr>
        <form action="users/cpass" method="post">
          <input type="hidden" id="cid" name="id" value="" />
          <div class="row" id="change_password_section" style="display:none;">
            <div class="col-3">
              <label for="name">Password</label>
            </div>
            <div class="col-3">

              <input type="password" class="form-control" id="password" name="password" placeholder="" value="" />
            </div>
            <div class="col-6">
              <div class="form-group" style="text-align: right;">
                <button type="submit" class="btn btn-primary" id="btn_ch_password">Change Password</button>
              </div>
            </div>
          </div>
        </form>
        <div class="row" id="delet_user" style="display:none;">
          <div class="col-12">
            <form action="users/delete" method="post">
              <input type="hidden" id="did" name="id" value="" />
              <button type="submit" class="btn btn-danger">Delete User</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>



<?php $this->load->view('layout/down'); ?>

<script>
  $(document).ready(function() {
    $('#users').DataTable();
  });

  $('.btn-success').each(function() {
    var $this = $(this);

    $this.on("click", function() {
      $('#form_edit').attr('action', '<?php echo base_url('users/edit'); ?>');
      $('#modal_title').html("Edit User");
      $('#btn_user_add').html("Save");
      $('#eid').val($(this).data('id'));
      $('#ename').val($(this).data('name'));
      $('#erole_id').val($(this).data('role_id'));
      $('#eemail').val($(this).data('email'));
      $('#ephone').val($(this).data('phone'));
      $('#edescription').val($(this).data('description'));

      $("#epassword").prop('required', false);

      $('#did').val($(this).data('id'));
      $('#cid').val($(this).data('id'));
      $('#delet_user').show();
      $('#change_password_section').show();
      $('#password_section').hide();
    });
  });


  $('#btn_add_user').on("click", function() {

    $('#modal_title').html("Add User");
    $('#btn_user_add').html("Add User");
    $('#form_edit').attr('action', '<?php echo base_url('users/add'); ?>');

    $('#eid').val("");
    $('#ename').val("");
    $('#erole_id').val(0);
    $('#email').val("");
    $('#phone').val("");
    $('#description').val("");

    $("#epassword").prop('required', true);

    $('#password_section').show();
    $('#change_password_section').hide();
    $('#eid').val("");
  });
</script>