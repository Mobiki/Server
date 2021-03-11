<?php

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();

use Firebase\JWT\JWT;

class Test extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {

        $key = "example_key";
        $payload = array(
            "iss" => "http://example.org",
            "aud" => "http://example.com",
            "iat" => 1356999524,
            "nbf" => 1357000000
        );

        /**
         * IMPORTANT:
         * You must specify supported algorithms for your application. See
         * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
         * for a list of spec-compliant algorithms.
         */
        $jwt = JWT::encode($payload, $key);
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        //print_r($jwt);
        //echo ("-------------");
        //print_r($decoded);
        //print_r("------------------");

        /*
 NOTE: This will now be an object instead of an associative array. To get
 an associative array, you will need to cast it as such:
*/

        $decoded_array = (array) $decoded;
        //print_r($decoded_array);
        //print_r("------------------");
        /**
         * You can add a leeway to account for when there is a clock skew times between
         * the signing and verifying servers. It is recommended that this leeway should
         * not be bigger than a few minutes.
         *
         * Source: http://self-issued.info/docs/draft-ietf-oauth-json-web-token.html#nbfDef
         */
        JWT::$leeway = 60; // $leeway in seconds

        //print_r($jwt);

        $decoded = JWT::decode($jwt, $key, array('HS256'));
        //print_r("------------------");
        //print_r($decoded);


        //print_r("------------------");
        //print_r("------------------");
        //print_r("------------------");







        $privateKey = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIICWwIBAAKBgF0uZPMUdMNwDRFdNolpPhGhtR3ZQ3nttrLc3nhtOIkEVAMRQVRO
dvPp6JwLPz+hzn9TDlnDL/LZ8T2UyMChS1rv2HDDz3LqLJCH+pV3Gn/cWsDqxT4n
JLFM79440/EmCbbT2jWO7xXCNXvwL0ZYv9wM+Ed8tQXgY1x1tzJHB6yvAgMBAAEC
gYBXg+tsIhpINEURueouxJl3Fdl1X0jwi0K8WpTXpj0i8t20w9AHzmoKS/YcGLQe
n2nCS89+nsO54tegbszdnp+WdlCdZim04LjqC264W7a+brzilntNiKPz4xdTL5GJ
WmZMOJPpgMH0j9fs6gbIcycT0e7hXrrBKkXs1uahBCTHgQJBAKTdqGMO6akRY8ix
17eLPMooZE8IbfaQEvZ4nlfATJP8jR6jnh3iHmGnJiyvqP7QNZN5me8Q4rGYE5uC
LLd28gMCQQCQsJG3TE1R84kYFfOKQrFQXpvhBSGf+KaPYcu6mAfwjBqbU5jAyncj
BCI/2zzAryOK2hj3qf5tyHXIn+1cKxDlAkEAokHVG8jthouq3TbKy8Wpiny+XFo7
f1LElvaXQF3uACeq6+C0GU0WAZ30ID6x4Dciw4YGThccRRUbFw3C3L2f6QJAYuqT
dACSC6i23OSE7szRc+R6JMfhSQAwvm1ZXmN5ahYeSnpIP+UqtaGp2IYFbqVNYyvf
TdHFwz/8ZgAPwacfkQJAR68V5ZqQ6gDtR/pPHZ5z2YJ9es9Hn+61gFjUF7wrsbJE
F8Ra5IGTsemOwjGBNduSSJpTfkBc4Lygc9jkh/hFIg==
-----END RSA PRIVATE KEY-----
EOD;

        $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIGeMA0GCSqGSIb3DQEBAQUAA4GMADCBiAKBgF0uZPMUdMNwDRFdNolpPhGhtR3Z
Q3nttrLc3nhtOIkEVAMRQVROdvPp6JwLPz+hzn9TDlnDL/LZ8T2UyMChS1rv2HDD
z3LqLJCH+pV3Gn/cWsDqxT4nJLFM79440/EmCbbT2jWO7xXCNXvwL0ZYv9wM+Ed8
tQXgY1x1tzJHB6yvAgMBAAE=
-----END PUBLIC KEY-----
EOD;

        $payload = array(
            "iss" => "example.org",
            "aud" => "example.com",
            "iat" => 1356999524,
            "nbf" => 1357000000
        );

        $jwt = JWT::encode($payload, $privateKey, 'RS256');
        echo "Encode:\n" . print_r($jwt, true) . "\n";

        $decoded = JWT::decode($jwt, $publicKey, array('RS256'));

        /*
 NOTE: This will now be an object instead of an associative array. To get
 an associative array, you will need to cast it as such:
*/

        $decoded_array = (array) $decoded;
        //echo "Decode:\n" . print_r($decoded_array, true) . "\n";
    }
}
