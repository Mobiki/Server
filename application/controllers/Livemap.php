<?php

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();


class Livemap extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("Devices_model");
        $this->load->model("Gateways_model");
    }

    public function index()
    {

        $dosya = '151_0_p.xml';

        //$tum = __DIR__.'/../../../resources/mapAssets/xml/'.$dosya;
        $tum = __DIR__.'/../../151/'.$dosya;

        $xml = file_get_contents($tum);

        //$yeniArr = $this->gatewaysInfo();
        $yeniArr = "";

        //return view('m2boyut', ['xml' => $xml,'gwList' => $yeniArr]);
        $devices=$this->Devices_model->getAll();

        $data = array(
            'id' => '',
            'pageName' =>"Map",
            'xml'=>$xml,
            'gwList'=>$yeniArr,
            'devices'=>$devices,
        );
        $this->load->view('livemap',$data);
    }

    protected function gatewaysInfo()
    {
        
        $json = file_get_contents('https://iot.mobiki.link/api/getGateways');

        $arr = json_decode($json,1);

        $yeniArr = [];
        foreach ($arr as $item)
        {
            $date1=date_create($item['updated_at']);
            $date2=date_create(date("Y-m-d H:i:s"));
            $diff=date_diff($date1,$date2);

            $item['description'] = str_replace("\n", '', $item['description']);

            $bol = explode(',', $item['location']);

            $item['lat'] = trim($bol[0]);
            $item['lon'] = trim($bol[1]);

            $item['isActive'] = 'active';
            if($diff->s > 89 || $diff->d >= 1 || $diff->i >= 1 || $diff->h >= 1)
                $item['isActive'] = 'inactive';

            $yeniArr[] = $item;
        }

        return $yeniArr;
    }



    public function getLastDeviceInfo()
    {
        $devicemac = $this->input->get("mac");
        
        $client = new Predis\Client([
            //'scheme' => 'tcp',
            'host'   => $this->config->item('redis_host'),
            //'port'   => $this->config->item('redis_port'),
            'password' => $this->config->item('redis_password')
        ]);

        $date=[];
  
            $userget = json_decode($client->get("user:".$devicemac), true);

            $gwinfo = $this->Gateways_model->getDetail($userget["gateway"]);

            $userget["location"]=(string)($gwinfo[0]["lat"]+($userget["rssi"]/10000000)).", ".(string)($gwinfo[0]["lng"]+($userget["rssi"]/10000000));
            $userget["personName"]=$devicemac;
            $userget["gw_name"]=$devicemac;

            //print_r($userget);
            
            //die();
        
        header('Content-Type: application/json');
        echo json_encode($userget);

        //$url = 'http://iot.mobiki.link/api/getLastDeviceInfoLatLon';
/*
        if (isset($_GET['mac']))
            $url .= '?mac=' . $_GET['mac'];

        $json = file_get_contents($url);
        $arr = json_decode($json);

        if (!isset($_GET['mac']))
            return response()->json($arr[0], 200);
        else {

            if (isset($_GET['test']))
                return response()->json($arr, 200);

            //pp(count($arr));

            $toplamArr = [];
            $oncekiGW = [
                'gw_mac' => '',
                'startTime' => '',
                'endTime' => '',
                'repeatCount' => '',
                'obj' => '',
            ];
            for ($i = 0; $i < count($arr); $i++) {

                //pp(($oncekiGW['gw_mac'] != $arr[$i]->gw_mac),0,1);

                    if ($oncekiGW['gw_mac'] != $arr[$i]->gw_mac)
                    {
                        // normal kontroller $toplamArr[(count($toplamArr)+1)] = [];

                        $oncekiGW['gw_mac'] = $arr[$i]->gw_mac;
                        $oncekiGW['startTime'] = $arr[$i]->created_at;
                        $oncekiGW['endTime'] = '';
                        $oncekiGW['obj'] = $arr[$i];
                        $oncekiGW['repeatCount'] = 1;
                        $oncekiGW['endTime'] = $arr[$i]->created_at;

                        $toplamArr[(int)count($toplamArr)+1] = $oncekiGW;

                        //$oncekiGW = [];

                    } else {

                        $oncekiGW['endTime'] = $arr[$i]->created_at;
                        //$oncekiGW['sure'] = 8;
                        $oncekiGW['repeatCount'] = ($oncekiGW['repeatCount']+1);
                        $toplamArr[(int)count($toplamArr)] = $oncekiGW;

                        //$oncekiGW['gw_mac'] = $arr[$i]->gw_mac;
                        //$oncekiGW['startTime'] = $arr[$i]->created_at;
                        //$oncekiGW['endTime'] = '';
                        //$oncekiGW['obj'] = $arr[$i];
                        //$oncekiGW['repeatCount'] = 1;

                    }

            }

            //asort($toplamArr);

            return response()->json($toplamArr, 200);
        }
*/
    }
}