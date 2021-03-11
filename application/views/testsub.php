<?php 



require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();

$client = new Predis\Client([
    'scheme' => $this->config->item('redis_scheme'),
    'host'   => $this->config->item('redis_host'),
    'port'   => $this->config->item('redis_port'),
    'password' => $this->config->item('redis_auth')
]);


$client->pubSubLoop(['subscribe' => 'gateways'], function ($l, $msg) {
    if ($msg->payload === 'unsub') {
        return false;
    } else {
        echo "$msg->payload on $msg->channel", PHP_EOL;
    }
});


/*$pubsub = $client->pubSubLoop();

$pubsub->subscribe('gateways');


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
                     PHP_EOL, "  {$message->payload}", PHP_EOL, PHP_EOL;
            }
            break;
    }
}
die();*/

?>