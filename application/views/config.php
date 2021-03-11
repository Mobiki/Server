<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Config</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <style>
        #dberror {
            display: none;
        }
    </style>
</head>

<body>
    <div class="jumbotron" id="jumbotron" style="display: block;">
        <h1 class="display-4">OpenMobiki</h1>
        <p class="lead">
            <hr class="my-4">
            <p>
                <p class="lead">
                    <a class="btn btn-primary btn-lg" href="#" role="button" onclick="start()">Config</a>
                </p>
    </div>

    <div id="firstconfig" style="display: none;">
        <div id="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 text-center"><img src="<?php echo base_url('assets/img/mobiki_logo.svg'); ?>" style="width: 150px; margin-top: -80px; margin-bottom: -55px;" /></div>

                </div>
                <hr>
                <br>
                <form id="dbform">
                    <div class="row">
                        <div class="col-4">
                        </div>
                        <div class="col-2">
                            Database Name
                        </div>
                        <div class="col-2">
                            <input type="text" class="form-control" name="dbname" value="openmobiki" />
                        </div>
                        <div class="col-4">
                            This name of database you want to run OpenMobiki in.
                        </div>
                    </div><br>
                    <div class="row">
                        <div class="col-4">
                        </div>
                        <div class="col-2">
                            User Name
                        </div>
                        <div class="col-2">
                            <input type="text" class="form-control" name="dbusername" value="root" />
                        </div>
                        <div class="col-4">
                            Your MySql username
                        </div>
                    </div><br>
                    <div class="row">
                        <div class="col-4">
                        </div>
                        <div class="col-2">
                            Password
                        </div>
                        <div class="col-2">
                            <input type="text" class="form-control" name="dbpassword" value="" />
                        </div>
                        <div class="col-4">
                            ...and your MySql password.
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-4">
                        </div>
                        <div class="col-2">
                            Database Host
                        </div>
                        <div class="col-2">
                            <input type="text" class="form-control" id="dbhost" name="dbhost" value="localhost" />
                        </div>
                        <div class="col-4">

                        </div>
                    </div>
                </form>
                <br>


                <hr>
                <div class="row">
                    <div class="col-3">
                    </div>
                    <div class="col-3">

                    </div>
                    <div class="col-3">
                        <a class="btn btn-primary btn-sm" href="#" role="button" onclick="dbtest()">Update Databese. Next: Set Admin User</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="secondconfig" style="display: none;">
        <div id="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-5"></div>
                    <div class="col-5"><img src="<?php echo base_url('assets/img/mobiki_logo.svg'); ?>" style="width: 150px; margin-top: -80px; margin-bottom: -55px;" /></div>
                    <div class="col-5"></div>
                </div>
                <hr>
                <br>
                <form id="admin" action="<?php echo base_url("config/setadmin");?>" method="post">
                    <div class="row">
                        <div class="col-4">
                        </div>
                        <div class="col-2">
                            Company Name
                        </div>
                        <div class="col-2">
                            <input type="text" class="form-control" name="cname" value="" required />
                        </div>
                        <div class="col-4">
                        </div>
                    </div><br>

                    <div class="row">
                        <div class="col-4">
                        </div>
                        <div class="col-2">
                            Name Surname
                        </div>
                        <div class="col-2">
                            <input type="text" class="form-control" name="name" value="" required />
                        </div>
                        <div class="col-4">
                        </div>
                    </div><br>
                    <div class="row">
                        <div class="col-4">
                        </div>
                        <div class="col-2">
                            E-mail
                        </div>
                        <div class="col-2">
                            <input type="text" class="form-control" name="email" type="email" value="" required />
                        </div>
                        <div class="col-4">
                        </div>
                    </div><br>
                    <div class="row">
                        <div class="col-4">
                        </div>
                        <div class="col-2">
                            Password
                        </div>
                        <div class="col-2">
                            <input type="password" class="form-control" name="password" value="" required />
                        </div>
                        <div class="col-4">
                        </div>
                    </div>

                    <br>

                    <hr>
                    <div class="row">
                        <div class="col-3">
                        </div>
                        <div class="col-3">

                        </div>
                        <div class="col-3">
                            <button class="btn btn-primary btn-sm" href="#" type="submit" onclick="setAdmin()">Set Admin</button>
                        </div>
                        <div id="result"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>

<script>
    function start() {
        $('#jumbotron').hide();
        $('#firstconfig').show();
        $('#secondconfig').hide();
        $('#licence').hide();
    }

    function start2() {
        $('#jumbotron').hide();
        $('#firstconfig').hide();
        $('#secondconfig').show();
        $('#licence').hide();
    }

    function dbtest() {
        $.post("config/db", $("#dbform").serialize())
            .done(function(data) {
                //alert(data);
                //$( "#dbhost" ).val(data);
                $('#jumbotron').hide();
                $('#firstconfig').hide();
                $('#secondconfig').show();
                $('#licence').hide();
            })
            .fail(function() {
                alert("Error");
            });
    }


</script>

</html>