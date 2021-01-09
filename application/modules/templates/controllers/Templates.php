<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Templates extends MX_Controller {

    public function render($data=null){
       $this->bulma($data);
    }


    public function bulma($data){
        $this->load->view('bulma', $data);
    }

}
