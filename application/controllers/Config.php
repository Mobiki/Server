
<?php

class Config extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index(Type $var = null)
    {
        $this->session->sess_destroy();
        
        $data = array(
            'pageId' => 0,
            'pageName' => "Config",
        );
        $this->load->view('config', $data);
    }

    public function db(Type $var = null)
    {
        $dbname =  $this->input->post("dbname", true);

        if ($dbname != "") {
            $dbusername = $this->input->post("dbusername");
            $dbpassword = $this->input->post("dbpassword");
            $dbhost = $this->input->post("dbhost");

            $filename = "database.php";
            $ourFileHandle = fopen(APPPATH . '/config/' . $filename, 'w');
            $written =  '<?php 
$active_group = "default";
$query_builder = TRUE;

$db["default"] = array(
	"dsn"	=> "mysql:host=' . $this->input->post("dbhost") . ';dbname=' . $dbname . '",
	"hostname" => "' . $dbhost . '",
	"username" => "' . $dbusername . '",
	"password" => "' . $dbpassword . '",
	"database" => "' . $dbname . '",
	"dbdriver" => "mysqli",
	"dbprefix" => "",
	"pconnect" => FALSE,
	"db_debug" => (ENVIRONMENT !== "production"),
	"cache_on" => FALSE,
	"cachedir" => "",
	"char_set" => "utf8",
	"dbcollat" => "utf8_general_ci",
	"swap_pre" => "",
	"encrypt" => FALSE,
	"compress" => FALSE,
	"stricton" => FALSE,
	"failover" => array(),
	"save_queries" => TRUE
);';

            fwrite($ourFileHandle, $written); //write new db connect file
            fclose($ourFileHandle); //file close


            redirect("config/creatTables?dbname=" . $dbname);
        }
    }

    public function creatTables()
    {
        $dbname =  $this->input->get("dbname");

            if ($dbname != "") {

                try {
                    $sql = file_get_contents(APPPATH . '../openmobiki.sql');
                    $sqls = explode(';', $sql);

                    foreach ($sqls as $key => $value) {

                        $statment = $sqls[$key] . ";";
                        $this->db->query($statment);
                    }
                } catch (Exception $e) {
                    var_dump($e->getMessage());
                }
            } else {

                echo ("db yok");
            }
        
    }

    public function license()
    {
        $filename = "license.php";
        $ourFileHandle = fopen(APPPATH . '/config/' . $filename, 'w');

        $written =  '<?php $config["key"]="";  ';

        fwrite($ourFileHandle, $written);

        fclose($ourFileHandle);
    }

    public function setadmin()
    {
        $cname =  $this->input->post("cname");
        $name =  $this->input->post("name");
        $email =  $this->input->post("email");
        $password =  $this->input->post("password");

        $data = array(
            'role_id' => 1,
            'name' => $name,
            'email' => $email,
            'password' => md5($password . md5('4671dfb2178c8f4b231f94a2e1ae675e')),
            'phone' => '',
            'description' => '',
            'token' => md5((string) time() . md5('b88d0391a211c286feee919055e7e75d')),
        );

        $this->db->insert('users', $data);

        $cdata = array(
            'name' => $cname,
            'token' => '',
        );

        $this->db->insert('company', $cdata);

        redirect("login");
    }
}
