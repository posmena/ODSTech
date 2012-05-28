<?php

class configuration{

         //const APPROOT = 'C:\\Users\\Mike\\Documents\\ODST\\ODSTech\\public_html\\';
		  const APPROOT = '/var/www/odst-live/';
       // system settings
        public $debugMode = false;
        public $scriptDebugMode = false;

        private $appPath = "";
        private $dateFormat = "jS F Y - H:i";

        // Get / Set settings
        public function set_appPath($url){
                $this->appPath = $url;
                return $this->appPath;
        }

        public function get_appPath(){
                return $this->appPath;
        }

        public function get_debugMode(){
                return $this->debugMode;
        }

        public function get_dbServer(){
                return 'localhost';
        }

        public function get_dbUser(){
                return 'odstech';
        }

        public function get_dbPassword(){
                return 'sailing1';
        }

        public function getWebgainsUser(){
                return 'odst';
        }

        public function getWebgainsPass(){
                return 'sailing1';
        }

        public function getWebgainsCampaign(){
                return 106558;
        }

        public function getAwinUser(){
                return 12345;
        }

        public function getAwinAPIKey(){
                return 'kms9kasd290ie98wesedkd0';
        }

        public function getDateFormat(){
                return $this->dateFormat;
        }


}

?>
