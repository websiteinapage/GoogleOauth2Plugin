<?php
/** @license MIT
 * 
Copyright (c) 2014 Uchenna Chilaka

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
 * 
 */
        
App::uses('Component', 'Controller');
App::uses('Curl', 'Component');
// declare unique app namespace APP_NAME

# Path to Google_Client Class
require_once GOOGLE_OAUTH2_PLUGIN_BASE . 'Vendor' . DS . 'google' . DS . 'google-api-php-client' . DS . 'src' . DS . "Google_Client.php";
# Path to Google_Oauth2Service class
require_once GOOGLE_OAUTH2_PLUGIN_BASE . 'Vendor' . DS . 'google' . DS . 'google-api-php-client' . DS . 'src' . DS . "contrib" . DS . "Google_Oauth2Service.php";

class GoogleOauth2Component extends Component {
    //put your code here
    public $oauth;
    public $google;
    public $userinfo;
    const OAUTH_TOKEN_INDEX = "goa_tokens";
    const OAUTH_USER_INDEX = "goa_user";
    
    public function initialize(\Controller $controller) {
        parent::initialize($controller);
    }
    
    public function init($config, $scopes) {
        session_start();
        $this->google = new Google_Client();
        $this->config = $config;
        $this->google->setClientId($config['client_id']);
        $this->google->setClientSecret($config['client_secret']);
        $this->google->setRedirectUri($config['redirect_url']);
        // find scope configuration
        $this->google->setScopes($scopes);
        $this->google->setUseObjects(false);
        $this->oauth = new Google_Oauth2Service($this->google);
    }
    
    public function connect(\Controller $controller, $config, $scopes = null, $api_mode=false) {
        $this->init($config, $scopes);
        
        if(!empty($_REQUEST['code'])):
            $this->requestAccessToken($config, $scopes);
            // if it makes it here
            $this->ready = true;
        else:    
            if($this->getTokens()):
                $this->google->setAccessToken($this->getTokens());
                $this->userinfo = $this->verify_credentials($controller, $this->getTokens(), $api_mode);
                // if it makes it here
                if(!empty($this->userinfo)):
                    $this->ready = true;
                endif;
                return $this->userinfo;
            else:
                if($api_mode):
                    return array('success'=>false);
                else:
                    $controller->redirect($this->getAuthUrl());
                endif;
            endif;
        endif;
    }
    
    function requestAccessToken($config, $scopes) {
        if(!empty($_REQUEST['code'])):
            $code = filter_var($_REQUEST['code'], FILTER_SANITIZE_STRING);
             if(!$this->google):
                 $this->init($config, $scopes);
             endif;
            $this->oauth = new Google_Oauth2Service($this->google);
            $token = $this->google->authenticate($code);
            $token_vars = json_decode($token, true);
            if(!empty($token_vars['access_token'])):
                // got token
                $this->userinfo = $this->oauth->userinfo->get();
                if(!empty($this->userinfo)):
                    $this->setUser($this->userinfo['id'], $token);
                    $this->ready = true;
                endif;
            endif;
        endif;
        return $this->getTokens();
    }
    
    private function authenticate(\Controller $controller, $code) {
        // $this->service = new Google_DriveService($this->google);
        $this->oauth = new Google_Oauth2Service($this->google);
        $token = $this->google->authenticate($code);
        $this->userinfo = $this->verify_credentials($controller, $token);
        if(!empty($this->userinfo)):
            $this->setUser($this->user['id'], $token);
            $this->ready = true;
        else:
            $controller->redirect($this->getAuthUrl());
        endif;
    }
    
    public function verify_credentials(\Controller $controller, $credentials, $api_mode=false) {
        // TODO: Use the oauth2.tokeninfo() method instead once it's
        //       exposed by the PHP client library
        $this->google->setAccessToken($credentials);
        // attempt to refresh 
        $tokens = json_decode($credentials, true);
        $now = time();
        $expired = 0;
        // get a new access token
        if(!empty($tokens['refresh_token'])):
            $now = time();
            $expired =$tokens['created']+$tokens['expires_in'];
            if($now<$expired):
                $this->google->refreshToken($tokens['refresh_token']);
            endif;
        endif;
        
        if($now>$expired):
            if($api_mode):
                return array('success'=>false, 'message'=>'Authentication failed');
            else:
                $controller->redirect($this->getAuthUrl());
            endif;
        endif;
        
        $user = $this->oauth->userinfo->get();
        if(empty($user)):
            if($api_mode):
                return array('success'=>false, 'message'=>'Authentication failed');
            else:
                $controller->redirect($this->getAuthUrl());
            endif;
        else:
            return $user;
        endif;
    }
    
    public function isReady() {
        return $this->ready;
    }
    
    function getAuthUrl() {
        try {
            $authUrl = $this->google->createAuthUrl();
            return $authUrl;
            //return array('success'=>true, 'authUrl'=>$authUrl);
        } catch (Exception $ex) {
            return false;
            // return array('success'=>false, 'message'=>$ex->getMessage());
        }
    }
    
    private function setUser($id, $token) {
        $_SESSION[GO2ANS . self::OAUTH_USER_INDEX]['user'] = array(
            'id'=>$id
        );
        $_SESSION[GO2ANS . self::OAUTH_USER_INDEX]['user']['token'] = json_decode($token, true);
    }
    /*
    private function setUserId($id) {
        $_SESSION[self::OAUTH_USER_INDEX]['id'] = $id;
    }
    */
    function getUser() {
        if (isset($_SESSION[GO2ANS . self::OAUTH_USER_INDEX])) {
          return $_SESSION[GO2ANS . self::OAUTH_USER_INDEX]['user'];
        }
    }
    
    private function getUserId() {
        $user = $this->getUser();
        if(!empty($user['token'])):
            return $user['id'];
        endif;
    }
    
    public function cleanUser() {
        session_start();
        unset($_SESSION[GO2ANS . self::OAUTH_USER_INDEX]);
    }
    
    public function getTokens() {
        $user = $this->getUser();
        if(!empty($user['token'])):
            return json_encode($user['token']);
        endif;
    }
    
    
}
