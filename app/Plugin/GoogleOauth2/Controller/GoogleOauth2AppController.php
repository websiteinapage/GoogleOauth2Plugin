<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GoogleOauth2AppController
 *
 * @author uchilaka
 */
class GoogleOauth2AppController extends AppController {
    //put your code here
    public function beforeFilter() {
        parent::beforeFilter();
        // load bootstrap
        // $plugin_dir = dirname(__DIR__);
        // require_once $plugin_dir . DS . 'Config' . DS . 'bootstrap.php';
    }
}
