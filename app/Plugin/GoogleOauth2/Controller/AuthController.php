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

class AuthController extends GoogleOauth2AppController {
    public $uses = array('GoogleOauth2.Auth');
    public $components = array('GoogleOauth2.GoogleOauth2', 'GoogleOauth2.Curl');
    public $scopes = array(
        'https://www.googleapis.com/auth/userinfo.email',
        'https://www.googleapis.com/auth/userinfo.profile'
    );
    private $config = array();
    public $oauth;
    public $google;
    private $auth_url;
    private $redirect_url;
    const OAUTH_TOKEN_INDEX = "goa_tokens";
    const OAUTH_USER_INDEX = "goa_user";
    
    public function beforeFilter() {
        parent::beforeFilter();
        // set client credentials - can be pulled from database
        $this->config = array(
            'client_id'=>'<CLIENT_ID>',
            'client_secret'=>'<CLIENT_SECRET>'
        );
        // set redirect URL
        $this->config['redirect_url'] = SITE_BASE . "google_oauth2/auth/connect";
    }

    public function index() {
        $api_mode = true;
        // attempt re-auth
        $connection = $this->GoogleOauth2->connect($this, $this->config, $this->scopes, $api_mode);
        
        if(!$this->GoogleOauth2->isReady()):
            // customize for purposes of plugin in your application
            $this->Session->setFlash("You are not logged in.");
        else:
            $this->set("user", $this->GoogleOauth2->userinfo);
        endif;
    }
    
    public function connect() {
        if(!empty($_REQUEST['code'])):
            $tokens = $this->GoogleOauth2->requestAccessToken($this->config, $this->scopes);
            $this->redirect(SITE_BASE . "google_oauth2/auth/index");
        else:
            $api_mode = false; // allow redirect
            $this->GoogleOauth2->connect($this, $this->config, $this->scopes, $api_mode);
            if($this->GoogleOauth2->isReady()):
                $this->set("user", $this->GoogleOauth2->userinfo);
            else:
                $this->Session->setFlash("Login failed.");
            endif;
        endif;
    }
    
    public function logout() {
        //$this->GoogleOauth2->init($this->config, $this->scopes);
        $this->GoogleOauth2->cleanUser();
        $this->redirect(SITE_BASE . "google_oauth2/auth/index");
    }
    
}
