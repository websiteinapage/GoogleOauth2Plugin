<?php 
// routing to the plugin start page
Router::connect('/', array('controller'=>'google_oauth2', 'action'=>'auth', 'index'));
