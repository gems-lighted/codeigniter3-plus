<?php
class Main extends MX_Controller{
    private $data;
	function __construct(){
		parent::__construct();
        $this->data['module'] = 'main';
        $this->data['view_file'] = 'main.php';

	}


	public function index($data=null){
        $this->data['view_file'] = 'main.php';
        echo Modules::run('templates/render', $this->data);
		
	}

    public function test(){

        $this->load->library('tablebuildercrud');

        $rawData= Modules::run('schulung/getTable'); // provides array('structure' => xyxy;  and 'rawData'=> xxx)

        $Config = array(
             //'module' => $this->data['module']
            'module' => 'schulung',
            'renameFields' => array(
                'bezeichner' => 'Bezeichnung',
                'beschreibung' => 'Was gelehrt wird',
                'kosten' => 'Kosten',
                'zeiteinheit' => 'Zeiteinheit'
            ),
            'supressFields' => array(
                'id' => 'id'
            ),
            'enableNewEntry' => true,
            'allowRowDelete' => true
        );

        $this->tablebuildercrud->setCRUDTableData($rawData,$Config);

        $tblData =  $this->tablebuildercrud->getCRUDTableHTML();
        $this->data['table'] =              $tblData['tablestring'];
        $this->data['newRowTemplate'] =     $tblData['newRowTemplate'];
        $this->data['newRowTemplateIDs'] =  $tblData['newRowTemplateIDs'];
        echo Modules::run('templates/render', $this->data);

    }

    public function impressum(){
        $this->data['view_file'] = 'impressum.php';
        echo Modules::run('templates/render', $this->data);
    }

    public function aboutme(){
        $this->data['view_file'] = 'aboutme.php';
        echo Modules::run('templates/render', $this->data);
    }
    public function ajaxHandler(){

        $msg = $this->input->post();

        if (isset($msg['cmd'])){
            switch ($msg['cmd']){
                case 'updateField' : {
                    $res = $this->ajUpdateField($msg);
                    echo json_encode(array('res' => $res),JSON_FORCE_OBJECT);
                }
                break;
                case 'insertRow' : {
                    $res = $this->ajInsertNewRow($msg);
                    echo json_encode(array('res' => $res),JSON_FORCE_OBJECT);
                }
                    break;
                case 'deleteRow' : {
                    $res = $this->ajDeleteRow($msg);
                    echo json_encode(array('res' => $res),JSON_FORCE_OBJECT);
                }break;
                default : {
                echo json_encode(array('res' => 'unknown cmd received'),JSON_FORCE_OBJECT);
                die();
                }
            }
        }else{
            echo json_encode(null);
        }

    }

    private function ajDeleteRow($msg){
        $data = explode("-",$msg['payload']['id']);
        $sql = "DELETE FROM ".$data[1]." WHERE ID=".$data[0];
        $res = $this->db->query($sql);
        if ($res === true){
            $msgres['payload'] = array(
                'msg' =>  "record deleted",
                'id' => $msg['payload']['id']
            );
        }else{
            $msgres['payload'] = array(
                'msg' =>  "error while deleting",
                'id' => $msg['payload']['id']
            );
        }
        $msgres['cmd'] = 'insertRow';
        return $msgres;
    }

    private function ajInsertNewRow($msg){
        if (!isset($msg['payload']['idcombined'])){
            $res = array(
                'cmd' => 'insertRow',
                'payload' => "No suitable payload!"
            );
            return $res;
        }
        try{
            $idarr = explode("-", $msg['payload']['idcombined']);

            if ($idarr[0] == 0){        // request due to new record
                $sql = 'INSERT INTO '.$idarr[2].' ('.$idarr[1].') VALUE (\''.$msg['payload']['newdata'].'\');';
                $res = $this->db->query($sql);
                $newID = $this->db->insert_id();

                if ($res === true){
                    $msgres['payload'] = array(
                        'setVal' => $msg['payload']['newdata'],
                        'idcombined' => $msg['payload']['idcombined'],
                        'idNew' => $newID,
                        'msg' =>  "new record created"
                    );
                }else{
                    $msgres['payload'] = array(
                        'setVal' => $msg['payload']['newdata'],
                        'idcombined' => $msg['payload']['idcombined'],
                        'msg' =>  "error"
                    );
                }
                $msgres['cmd'] = 'insertRow';
                return $msgres;
            }
            die("wow... on here we should never arrive ....138 main.php");


        }catch (Exception $e){
            die ($e->getPrevious());
        }
    }

    private function ajUpdateField($msg){
        if (!isset($msg['payload']['idcombined'])){
            $res = array(
                'cmd' => 'updateField',
                'payload' => "No suitable payload!"
            );
            return $res;
        }

        try{
            $idarr = explode("-", $msg['payload']['idcombined']);

            if ($idarr[0] != 0){ // request not originated from new row in table; simple update
                $sql = 'UPDATE '.$idarr[2].' SET ' . $idarr[1] . ' = \'' . $msg['payload']['newdata'] . '\' WHERE id = ' .$idarr[0];
                $msgres = array();
                $msgres['cmd'] = 'updateField';

                $res = $this->db->query($sql);
                if ($res === true){
                    $msgres['payload'] = array(
                        'setVal' => $msg['payload']['newdata'],
                        'idcombined' => $msg['payload']['idcombined'],
                        'msg' =>  "field updated"
                    );
                }else{
                    $msgres['payload'] = array(
                        'setVal' => $msg['payload']['newdata'],
                        'idcombined' => $msg['payload']['idcombined'],
                        'msg' =>  "error"
                    );
                }
                $msgres['cmd'] = 'updateField';
                return $msgres;
            }

            die("wow... on here we should never arrive ....158 main.php");


        }catch (Exception $e){
            die ($e->getPrevious());
        }
    }



}

?>
