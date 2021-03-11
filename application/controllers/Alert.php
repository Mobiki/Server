<?php

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();

class Alert extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("Alert_model");
        $this->load->model("Devices_model");
        $this->load->model("Users_model");
        $this->load->model("Gateways_model");
        $this->load->model("Personnel_model");
        $this->load->model("Assets_model");
    }

    public function redis()
    {
        $client = new Predis\Client([
            'scheme' => $this->config->item('redis_scheme'),
            'host'   => $this->config->item('redis_host'),
            'port'   => $this->config->item('redis_port'),
            'password' => $this->config->item('redis_auth')
        ]);
        return $client;
    }

    public function index()
    {
        $devices = $this->Devices_model->get_all();
        $alert_rules = $this->Alert_model->get_all_alert_rules();
        $devices_type = $this->Devices_model->get_all_device_type();

        $data = array(
            'pageId' => '10',
            'pageName' => 'Alerts',
            'devices' => $devices,
            'alert_rules' => $alert_rules,
            'devices_type' => $devices_type,
        );
        $this->load->view("alert", $data);
    }

    public function alert_open_add()
    {
        $alert_key = $this->input->get("alert_key");
        $alert_rules_id = $this->input->get("alert_rules_id");
        $device_id = $this->input->get("device_id");
        $gateway_id = $this->input->get("gateway_id");
        $opened_person_id = $this->input->get("opened_person_id");
        $open_date = $this->input->get("open_date");

            $data = array(
                'alert_key' => $alert_key,
                'alert_rules_id' =>  $alert_rules_id,
                'device_id' =>  $device_id,
                'gateway_id' =>  $gateway_id,
                'opened_person_id' =>  $opened_person_id,
                'open_date' =>  $open_date,
                'status' =>  1,
            );
            $result = $this->Alert_model->insert_alert_open($data);
    }

    public function add()
    {
        $data = array(
            'name' => $this->input->post("name"),
            'device_id' => $this->input->post("device_id"),
            'device_type' => $this->input->post("device_type"),
            'sensor_value' => $this->input->post("sensor_value"),
            'equation' => $this->input->post("equation"),
        );

        $result = $this->Alert_model->insert_alert_rule($data);

        if ($result) {
            $this->toredis(); //Send to redis
            redirect('alert');
        } else {
            echo "Error - Alert - insert";
        }
    }

    public function edit()
    {
        $id = $this->input->post("id", true);

        $data = array(
            'name' => $this->input->post("name", true),
            'device_id' => $this->input->post("device_id", true),
            'device_type' => $this->input->post("device_type", true),
            'sensor_value' => $this->input->post("sensor_value", true),
            'equation' => $this->input->post("equation", true),
        );

        $result = $this->Alert_model->update_alert_rule($id, $data);

        if ($result) {
            $this->toredis(); //Send to redis
            redirect('alert');
        } else {
            echo "Error - Alert - Update";
        }
    }

    public function delete()
    {
        $id = $this->input->post("id", true);

        $result = $this->Alert_model->delete_alert_rule($id);

        if ($result) {
            $this->toredis(); //Send to redis
            redirect('alert');
        } else {
            echo "Error - Alert - Delete";
        }
    }

    public function toredis()
    {
        $client = $this->redis();
        $alert_rules = $this->Alert_model->get_all_alert_rules();
        $client->set("alert_rules", json_encode($alert_rules));
    }


    public function logs()
    {
        $devices = $this->Devices_model->get_all();
        $users = $this->Users_model->get_all();
        $alert_logs = $this->Alert_model->get_all_alert_logs();
        $alert_rules = $this->Alert_model->get_all_alert_rules();
        $gateweys = $this->Gateways_model->get_all();
        $data = array(
            'pageId' => '10.1',
            'pageName' => 'Alert Logs',
            'devices' => $devices,
            'users' => $users,
            'alert_logs' => $alert_logs,
            'gateweys' => $gateweys,
            'alert_rules' => $alert_rules,
        );
        $this->load->view("alert_logs", $data);
    }



    //###############alert##################################################################################

    public function get_all_open_alerts()
    {
        if ($this->input->get("pageID")) {
            $pageId = $this->input->get("pageID");
        } else {
            $pageId = 0;
        }

        $client = $this->redis();
        $alert_rules = json_decode($client->get("alert_rules"), true);
        $devices = json_decode($client->get("devices"), true);
        $device_types = json_decode($client->get("device_type"), true);
        $gateways = json_decode($client->get("gateways"), true);
        $personnel = json_decode($client->get("personnel"), true);

        $alertkeys = $client->keys("alert:*");
        foreach ($alertkeys as $key => $value) {
            $alert = json_decode($client->get($value), true);
            if (isset($alert["status"]) && $alert["status"] == 1) {
                $alert_key = $alertkeys[$key];
                $mac = $alert["mac"];
                $epoch = $alert["epoch"];
                $gateway_mac = $alert["gateway"];
                $status = $alert["status"];
                $alert_value = $alert["alert"];
                $device_id = "";
                $device_name = "";
                $alert_id = "";
                $alert_name = "";
                $device_type_name = "";
                $device_type_id = "";
                $gateway_name = "";
                $gateway_id = "";
                $gateway_location = "";
                //find device id and type_id
                foreach ($devices as $devices_key => $device) {
                    if ($device["mac"] == $mac) {
                        $device_id = $device["id"];
                        $device_name = $device["name"];
                        $device_type_id = $device["type_id"];
                        break;
                    }
                }
                //find person name
                foreach ($personnel as $key => $person) {
                    if ($person["device_id"] == $device_id) {
                        $device_name = $person["name"];
                    }
                }
                //find device type name
                foreach ($device_types as $device_types_key => $device_type) {
                    if ($device_type["id"] == $device_type_id) {
                        $device_type_name = $device_type["name"];
                        break;
                    }
                }
                //find gateway information
                foreach ($gateways as $gateways_key => $gateway) {
                    if ($gateway["mac"] == $gateway_mac) {
                        $gateway_name = $gateway["name"];
                        $gateway_id = $gateway["id"];
                        $gateway_location = $gateway["lat"] . ", " . $gateway["lng"];
                        break;
                    }
                }

                //find alert name all devices or one device
                foreach ($alert_rules as $alert_rules_key => $alert_rule) {
                    if ($alert_rule["device_id"] == 0) {
                        //ALL devices
                        if ($alert_rule["device_type"] == $device_type_id) {
                            if ($alert_rule["equation"] == "0") { //equation 0 "=" for sensor_value
                                if ($alert_rule["sensor_value"] == $alert_value) {
                                    $alert_name = $alert_rule["name"];
                                    $alert_id = $alert_rule["id"];
                                    $this->alert_parse($alert_name, $epoch, $device_name, $gateway_location, $gateway_name, $alert_key, $alert_id, $device_id, $gateway_id, $pageId);
                                }
                            }
                            if ($alert_rule["equation"] == "1") { //equation 1 ">" for sensor_value
                                if ($alert_value > $alert_rule["sensor_value"]) {
                                    $alert_name = $alert_rule["name"];
                                    $alert_id = $alert_rule["id"];
                                    $this->alert_parse($alert_name, $epoch, $device_name, $gateway_location, $gateway_name, $alert_key, $alert_id, $device_id, $gateway_id, $pageId);
                                }
                            }
                            if ($alert_rule["equation"] == "2") { //equation 2 "<" for sensor_value
                                if ($alert_value < $alert_rule["sensor_value"]) {
                                    $alert_name = $alert_rule["name"];
                                    $alert_id = $alert_rule["id"];
                                    $this->alert_parse($alert_name, $epoch, $device_name, $gateway_location, $gateway_name, $alert_key, $alert_id, $device_id, $gateway_id, $pageId);
                                }
                            }
                        }
                    } else {
                        //one devices
                        if ($alert_rule["device_id"] == $device_id) {
                            if ($alert_rule["equation"] == "0") { //equation 0 "=" for sensor_value
                                if ($alert_rule["sensor_value"] == $alert_value) {
                                    $alert_name = $alert_rule["name"];
                                    $alert_id = $alert_rule["id"];
                                    $this->alert_parse($alert_name, $epoch, $device_name, $gateway_location, $gateway_name, $alert_key, $alert_id, $device_id, $gateway_id, $pageId);
                                }
                            }
                            if ($alert_rule["equation"] == "1") { //equation 1 ">" for sensor_value
                                if ($alert_value > $alert_rule["sensor_value"]) {
                                    $alert_name = $alert_rule["name"];
                                    $alert_id = $alert_rule["id"];
                                    $this->alert_parse($alert_name, $epoch, $device_name, $gateway_location, $gateway_name, $alert_key, $alert_id, $device_id, $gateway_id, $pageId);
                                }
                            }
                            if ($alert_rule["equation"] == "2") { //equation 2 "<" for sensor_value
                                if ($alert_value < $alert_rule["sensor_value"]) {
                                    $alert_name = $alert_rule["name"];
                                    $alert_id = $alert_rule["id"];
                                    $this->alert_parse($alert_name, $epoch, $device_name, $gateway_location, $gateway_name, $alert_key, $alert_id, $device_id, $gateway_id, $pageId);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function alert_parse($alert_name, $epoch, $device_name, $gateway_location, $gateway_name, $alert_key, $alert_id, $device_id, $gateway_id, $pageId)
    {
        echo '<tr>';
        echo '<td>' . $alert_name . '</td>';
        echo '<td>' . date("d/m H:i:s", $epoch) . '</td>';
        echo '<td>' . $device_name . '</td>';
        echo '<td>' . $gateway_name . '</td>';
        if ($pageId != "1") {
            echo '<td>';
            echo '<div class="custom-control custom-switch">'; ?>
            <input type="checkbox" class="custom-control-input" onClick="$.get('<?php echo base_url('alert/suspend_alert'); ?>?alert_key=<?php echo $alert_key; ?>&alert_rules_id=<?php echo $alert_id; ?>&device_id=<?php echo $device_id; ?>&gateway_id=<?php echo $gateway_id; ?>');" id="cs<?php echo $epoch; ?>" />
        <?php
                    echo '<label class="custom-control-label" for="cs' . $epoch . '"> Suspend Alarm</label>';
                    echo '</div>';

                    echo '</td>';
                }
                echo '</tr>';
            }



            public function get_all_suspended_alerts()
            {
                $suspended_alerts = $this->Alert_model->get_all_suspended_alerts();
                $alert_rules = $this->Alert_model->get_all_alert_rules();
                $users = $this->Users_model->get_all();
                $devices = $this->Devices_model->get_all();
                $personnel = $this->Personnel_model->get_all();
                $assets = $this->Assets_model->get_all();
                $gateways = $this->Gateways_model->get_all();

                foreach ($suspended_alerts as $key => $suspended_alert) {
                    $alert_log_id = $suspended_alert["id"];
                    $alert_key = $suspended_alert["alert_key"];
                    $alert_rules_id = $suspended_alert["alert_rules_id"];
                    $device_id = $suspended_alert["device_id"];
                    $gateway_id = $suspended_alert["gateway_id"];
                    $suspended_user_id = $suspended_alert["suspended_user_id"];
                    $suspend_date = $suspended_alert["suspend_date"];
                    $user_name = "";
                    $alert_name = "";
                    $device_name = "";
                    $personnel_name = "";
                    $asset_name = "";
                    $gateway_name = "";
                    foreach ($users as $key => $user) {
                        if ($suspended_alert["suspended_user_id"] == $user["id"]) {
                            $user_name = $user["name"];
                            break;
                        }
                    }
                    foreach ($alert_rules as $key => $alert_rule) {
                        if ($alert_rule["id"] == $alert_rules_id) {
                            $alert_name = $alert_rule["name"];
                            break;
                        }
                    }
                    foreach ($devices as $key => $device) {
                        if ($device["id"] == $device_id) {
                            $device_type = $device["type_id"];
                            $device_name = $device["name"];
                            break;
                        }
                    }
                    foreach ($gateways as $gateways_key => $gateway) {
                        if ($gateway["id"] == $gateway_id) {
                            $gateway_name = $gateway["name"];
                            $gateway_location = $gateway["lat"] . ", " . $gateway["lng"];
                            break;
                        }
                    }


                    echo '<tr>';
                    echo '<td>' . $alert_name . "</td>";
                    echo '<td>' . $suspend_date . '</td>';
                    echo '<td>' . $device_name . '</td>';
                    echo '<td>' . $gateway_name . '</td>';
                    echo '<td>' . $user_name . '</td>';
                    echo '<td>'; ?>
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" onClick="$.get('<?php echo base_url("alert/close_alert"); ?>?alert_key=<?php echo $alert_key; ?>&alert_log_id=<?php echo $alert_log_id; ?>');" id="css<?php echo $alert_log_id; ?>">
        <?php echo '<label class="custom-control-label" for="css' . $alert_log_id . '"> Close</label>';
                    echo '</div>';
                    echo '</td>';
                    echo '</tr>';
                }
            }

            public function get_all_closed_alerts()
            {
                $close_alerts = $this->Alert_model->get_all_closed_alerts();
                $alert_rules = $this->Alert_model->get_all_alert_rules();
                $users = $this->Users_model->get_all();
                $devices = $this->Devices_model->get_all();
                $personnel = $this->Personnel_model->get_all();
                $assets = $this->Assets_model->get_all();
                $gateways = $this->Gateways_model->get_all();
                
                foreach ($close_alerts as $key => $close_alert) {
                    $alert_log_id = $close_alert["id"];
                    $alert_key = $close_alert["alert_key"];
                    $alert_rules_id = $close_alert["alert_rules_id"];
                    $device_id = $close_alert["device_id"];
                    $gateway_id = $close_alert["gateway_id"];
                    $closed_user_id = $close_alert["closed_user_id"];
                    $close_date = $close_alert["close_date"];
                    $user_name = "";
                    $alert_name = "";
                    $device_name = "";
                    $personnel_name = "";
                    $asset_name = "";
                    $gateway_name = "";
                    foreach ($users as $key => $user) {
                        if ($close_alert["closed_user_id"] == $user["id"]) {
                            $user_name = $user["name"];
                            break;
                        }
                    }
                    foreach ($alert_rules as $key => $alert_rule) {
                        if ($alert_rule["id"] == $alert_rules_id) {
                            $alert_name = $alert_rule["name"];
                            break;
                        }
                    }
                    foreach ($devices as $key => $device) {
                        if ($device["id"] == $device_id) {
                            $device_type = $device["type_id"];
                            $device_name = $device["name"];
                            break;
                        }
                    }
                    foreach ($gateways as $gateways_key => $gateway) {
                        if ($gateway["id"] == $gateway_id) {
                            $gateway_name = $gateway["name"];
                            $gateway_location = $gateway["lat"] . ", " . $gateway["lng"];
                            break;
                        }
                    }
                    echo '<tr>';
                    echo '<td>' . $alert_name . "</td>";
                    echo '<td>' . $close_date . '</td>';
                    echo '<td>' . $device_name . '</td>';
                    echo '<td>' . $gateway_name . '</td>';
                    echo '<td>' . $user_name . '</td>';
                    echo '<td> Closed </td>';
                    echo '</tr>';
                }
            }

            public function suspend_alert()
            {
                $client = $this->redis();

                $user_data = $this->session->userdata('userdata');

                $alert_key = $this->input->get("alert_key");

                if ($user_data["id"] != 0 && $user_data["id"] != "") {
                    $data = array(
                        'alert_key' => $this->input->get("alert_key"),
                        'alert_rules_id' => $this->input->get("alert_rules_id"),
                        'device_id' => $this->input->get("device_id"),
                        'gateway_id' => $this->input->get("gateway_id"),
                        'suspended_user_id' => $user_data["id"],
                        'suspend_date' => date('Y-m-d H:i:s'),
                        'status' => 2,
                    );

                    $this->Alert_model->insert_alert_log($data);

                    $lightinfo = json_decode($client->get($alert_key), true);
                    $lightinfo["status"] = 2;
                    $lightsuspend = json_encode($lightinfo);
                    $client->set($alert_key, $lightsuspend);
                }
            }

            public function close_alert()
            {
                $client = $this->redis();
                $alert_log_id = $this->input->get("alert_log_id");
                $alert_key = $this->input->get("alert_key");

                $user_data = $this->session->userdata('userdata');
                //if ($user_data["id"] != 0 && $user_data["id"] != "") {
                $data = array(
                    'closed_user_id' => $user_data["id"],
                    'close_date' => date('Y-m-d H:i:s'),
                    'status' => 3,
                );
                $this->Alert_model->alert_close($alert_log_id, $data);
                $client->del($alert_key);
                //}
            }

            public function get_logs()
            {
                $device_id = $this->input->get("device_id");
                $gatewey_id = $this->input->get("gatewey_id");
                $user_id = $this->input->get("user_id");

                $start = $this->input->get("sDate");
                $finish = $this->input->get("fDate");
                $result = $this->Alert_model->get_alert_logs_where($device_id, $gatewey_id, $user_id, $start, $finish);
                print_r($result);
            }

            public function widget()
            {
                $this->load->view("alert_widget");
            }

            public function alerts()
            {
                $alert_logs = $this->Alert_model->get_all_alert_logs();


                

                $data = array(
                    'pageId' => '10.2',
                    'pageName' => 'Alerts',
                    'alert_logs' => $alert_logs,
                );
                $this->load->view("alerts", $data);
            }

            public function sub()
            {
                $client = new Predis\Client([
                    'scheme' => $this->config->item('redis_scheme'),
                    'host'   => $this->config->item('redis_host'),
                    'port'   => $this->config->item('redis_port'),
                    'password' => $this->config->item('redis_auth'),
                    //'read_write_timeout' => 0,
                ]);

                //$client = new Predis\Client($single_server + array('read_write_timeout' => 0));
                // Initialize a new pubsub consumer.
                $pubsub = $client->pubSubLoop();

                // Subscribe to your channels
                $pubsub->subscribe('gateways');

                // Start processing the pubsup messages. Open a terminal and use redis-cli
                // to push messages to the channels. Examples:
                //   ./redis-cli PUBLISH notifications "this is a test"
                //   ./redis-cli PUBLISH control_channel quit_loop
                foreach ($pubsub as $message) {
                    switch ($message->kind) {
                        case 'subscribe':
                            echo "Subscribed to {$message->channel}", PHP_EOL;
                            break;

                        case 'message':
                            if ($message->channel == 'control_channel') {
                                if ($message->payload == 'quit_loop') {
                                    echo 'Aborting pubsub loop...', PHP_EOL;
                                    $pubsub->unsubscribe();
                                } else {
                                    echo "Received an unrecognized command: {$message->payload}.", PHP_EOL;
                                }
                            } else {
                                echo "Received the following message from {$message->channel}:",
                                    PHP_EOL,
                                    "  {$message->payload}",
                                    PHP_EOL,
                                    PHP_EOL;
                            }
                            break;
                    }
                }

                // Always unset the pubsub consumer instance when you are done! The
                // class destructor will take care of cleanups and prevent protocol
                // desynchronizations between the client and the server.
                unset($pubsub);
            }
        }
