<!DOCTYPE html>
<html lang="en">
<?php $this->load->view("layout/head"); ?>

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
                <!-- Page Content -->


            </div>
            <!-- /.container-fluid -->

            <!-- Sticky Footer -->
            <?php  ?>
        </div>
        <!-- /.content-wrapper -->
    </div>
    <!-- /#wrapper -->

    <!-- Scroll to Top Button // Logout Modal-->
    <?php $this->load->view("layout/logoutmodal"); ?>
    <!-- JavaScript-->
    <?php $this->load->view("layout/scripts"); ?>
</body>

<script>

</script>

</html>