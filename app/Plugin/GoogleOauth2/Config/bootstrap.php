<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
define("SITE_BASE", "http://localhost/GoogleOauth2Plugin/");
$plugin_path = dirname(__DIR__) . DS;
define("GOOGLE_OAUTH2_PLUGIN_BASE", $plugin_path);
/** Namespace for Session Variables **/
define("GO2ANS", "go2ans_");
# Path to Google_Client Class
require_once GOOGLE_OAUTH2_PLUGIN_BASE . 'Vendor' . DS . 'google' . DS . 'google-api-php-client' . DS . 'src' . DS . "Google_Client.php";
# Path to Google_Oauth2Service class
require_once GOOGLE_OAUTH2_PLUGIN_BASE . 'Vendor' . DS . 'google' . DS . 'google-api-php-client' . DS . 'src' . DS . "contrib" . DS . "Google_Oauth2Service.php";
