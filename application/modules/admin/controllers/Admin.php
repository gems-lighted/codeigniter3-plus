<?php
/**
 * Created by PhpStorm.
 * User: dk2
 * Date: 13.12.20
 * Time: 20:53
 */
class Admin extends MX_Controller{
    private $data=array();
    private $cmds=array();
    function __construct(){
        parent::__construct();

        $this->cmds['cmd_mkdir'] = '/usr/bin/mkdir -p ';
        $this->cmds['cmd_chmod_dir'] = '/usr/bin/chmod 775 ';
        $this->cmds['cmd_chmod_file'] = '/usr/bin/chmod 664 ';

    //echo FCPATH; /var/www/servers/diagrin/htdocs/
        $this->load->model('mdl_admin' );
        $this->data['module'] = 'admin';
        $this->data['log'] = "";
    }

    public function mngModules(){

        $this->load->helper(array('form', 'url'));

        $this->load->library('form_validation');
        $this->form_validation->set_rules('modulename', 'Name of Module', 'required',
            array('required' => 'You must provide a %s.'));

        if ($this->form_validation->run() == FALSE)
        {
            $this->data['view_file'] = 'frmNewModule';
            echo Modules::run('templates/render', $this->data);
        }
        else
        {
            $moduleName = $this->input->post('modulename');
            $this->data['log'] .= $this->createfolder($moduleName);
            $this->data['log'] .= $this->CreateControllerFromTemplate($moduleName);
            $this->data['log'] .= $this->CreateModelFromTemplate($moduleName);
            $this->data['log'] .= $this->CreateViewFromTemplate($moduleName);
            $this->data['newmodulename'] = $moduleName;

            $this->data['view_file'] = 'frmNewModuleSuccess';
            echo Modules::run('templates/render', $this->data);

        }
    }

    public function mngModels(){


        $this->data['knownModules'] = $this->mngGetModelsFromDir();
        $this->data['knowndatatypeDB'] = $this->mdl_admin->get_was_kann_datenbank_an_datentypen();
        $this->data['addJSlibs'] = ' <script type="text/javascript" src="/js/admin-mngmodels.js"></script>';

        $this->data['view_file'] = 'frmTableCreator';
        echo Modules::run('templates/render', $this->data);
    }

    public function mngGetModelsFromDir(){
        $dirlist = scandir(__DIR__ . "/../../");
        $filter = array(
            '.', '..', 'admin', 'templates', 'main'
        );
        $dirlistclean = array();
        foreach ($dirlist as $d){
            if (!in_array($d, $filter)){
                array_push($dirlistclean, $d);
            }
        }
        return $dirlistclean;
    }

    public function ajaxHandler(){

        $msg = $this->input->post();
        if (isset($msg['cmd'])){
            switch ($msg['cmd']){
                case 'cmd_write2db' : {
                    $res = $this->ajCreateColumn($msg);
                    echo json_encode(array('res' => $res),JSON_FORCE_OBJECT);
                }
                    break;
                case 'cmd_btnGetFieldInfo' : {
                    $res = $this->ajGetFieldInfo($msg);
                    echo json_encode(array('res' => $res),JSON_FORCE_OBJECT);
                }
                    break;
                case 'cmd_listfields' : {
                    echo json_encode($this->ajListFields($msg));
                }
                    break;
                case 'cmd_btnDeleteField' : {
                    echo json_encode($this->ajDeleteField($msg));
                }
                    break;
                case 'cmd_updatefields' : {
                    echo json_encode($this->ajUpdateField($msg));
                }
                    break;
                default : {
                    echo json_encode(array('res' => 'unknown cmd received'),JSON_FORCE_OBJECT);
                    die();
                }
            }
        }else{
            echo json_encode(null);
        }

    }

    private function ajUpdateField($msg){
        if (empty($msg['payload']['txtModulename'])){
            $res = array(
                'cmd' => 'cmd_updatefields',
                'payload' => "There's no table (no module) yet defined!"
            );
            return $res;
        }
        $this->load->dbforge();
        $res=array();
        $table = $msg['payload']['txtModulename'];
        $fieldOldName = $msg['payload']['tmpName'];
        try{
            if ($this->db->table_exists($table)){
                if ($this->db->field_exists($fieldOldName,$table)){
                    $field_tmp = $this->hlpGetmysqlDatatype($msg['payload']);
                    $fields[$fieldOldName] = $field_tmp[$msg['payload']['txtName']];
                    $fields[$fieldOldName]['name'] = $msg['payload']['txtName'];
                    $this->dbforge->modify_column($table, $fields);
                    $res = array(
                        'cmd' => 'cmd_updatefields',
                        'payload' => "Field ".$fieldOldName." for module ".$msg['payload']['txtModulename']." updated!"
                    );
                    return $res;

                }else{
                    $res = array(
                        'cmd' => 'cmd_updatefields',
                        'payload' => "There's no field ".$msg['payload']['txtName']." for module ".$msg['payload']['txtModulename']." yet defined!"
                    );
                    return $res;
                }
            }else{
                $res = array(
                    'cmd' => 'cmd_updatefields',
                    'payload' => "There's no table ".$msg['payload']['txtModulename']." found!"
                );
                return $res;
            }
            return $res;
        }catch (Exception $e){
            die ($e->getPrevious());
        }

    }

    private function ajDeleteField($msg){
        $this->load->dbforge();
        if ($this->db->table_exists($msg['payload']['txtModulename'])){
            if ($this->db->field_exists($msg['payload']['fieldname'],$msg['payload']['txtModulename'])){
                $amntFields = count($this->db->field_data($msg['payload']['txtModulename']));
                if ($amntFields > 1){
                    $this->dbforge->drop_column($msg['payload']['txtModulename'], $msg['payload']['fieldname']);
                }else{
                    $this->dbforge->drop_table($msg['payload']['txtModulename']);
                }

            }
            $res = array(
                'cmd' => 'cmd_btnDeleteField',
                'txtModulename' => $msg['payload']['txtModulename'],
                'payload' => "Field ".$msg['payload']['fieldname']." has been deleted from table ".$msg['payload']['txtModulename']
            );
        }else{
            $res = array(
                'cmd' => 'showListFields',
                'payload' => "There's no field ".$msg['payload']['fieldname']." for module ".$msg['payload']['txtModulename']." yet defined!"
            );
        }
        return $res;
    }

    private function ajGetFieldInfo($msg){
        $res = array(
            'cmd' => 'cmd_btnGetFieldInfo',
            'payload' => $this->ajGetFieldInfo_doit($msg)
        );
        return $res;
        /*
         * {
  "res": {
    "cmd": "cmd_btnGetFieldInfo",
    "payload": {
      "Field": "employeenr",
      "Type": "int(10) unsigned",
      "Null": "NO",
      "Key": "",
      "Default": null,
      "Extra": ""
    }
  }
}
         *
         * */
    }
    private function ajGetFieldInfo_doit($msg){

        $query = $this->db->query('SHOW COLUMNS FROM '.$msg['payload']['txtModulename']);
        $entryfound=false;
        $foundrow = array();
        foreach ($query->result() as $row) {
            if ($row->Field == $msg['payload']['fieldname']){
                $foundrow =  $row;
                $entryfound = true;
                break;
            }
        }
        if ($entryfound){
            $r = array();
            $r['Field'] = $foundrow->Field;
            $r['Null'] =  $foundrow->Null;  // YES, NO
            $r['Default'] = $foundrow->Default;  // YES, NO
            $r['Extra'] = $foundrow->Extra;  // YES, NO
            // look for unsigend $condition ? 'foo' : 'bar';
            $r['Unsigned'] = !strpos($foundrow->Type, "unsigned") ? 'NO' : 'YES';  // YES, NO
            $tmp  = $this->hlpGetConstraint($foundrow->Type);
            if (isset($tmp['Type'])){$r['Type'] = $tmp['Type'];}
            if (isset($tmp['Constraint'])){$r['Constraint'] = $tmp['Constraint'];}
            $r['raw'] = $tmp['raw'];
            return $r;
        }else{
            return "wow... huge problem";
        }
        die("155 admin.php very bad.....");
    }

    private function ajListFields($msg){
        if (empty($msg['payload']['txtModulename'])){
            $res = array(
                'cmd' => 'showListFields',
                'payload' => "There's no table (no module) yet defined!"
            );
            return $res;
        }
        if ($this->db->table_exists($msg['payload']['txtModulename'])){
            $res = array(
                'cmd' => 'showListFields',
                'txtModulename' => $msg['payload']['txtModulename'],
                'payload' => $this->db->field_data($msg['payload']['txtModulename'])
            );
        }else{
            $res = array(
                'cmd' => 'showListFields',
                'payload' => "There's no table for module ".$msg['payload']['txtModulename']." yet defined!"
            );
        }
        return $res;

    }

    private function ajCreateColumn($msg){
        if (empty($msg['payload']['txtModulename'])){
            $res = array(
                'cmd' => 'showListFields',
                'payload' => "There's no table (no module) yet defined!"
            );
            return $res;
        }
        $this->load->dbforge();
        try{
            // prepare Field
            $fields = $this->hlpGetmysqlDatatype($msg['payload']);
            $table = $msg['payload']['txtModulename'];

            if ($this->db->table_exists($table)){
                $this->dbforge->add_field($fields);
                $this->dbforge->add_column($table, $fields);

            }else{
                $tblname = $msg['payload']['txtModulename'];
                $query = $this->db->query("CREATE TABLE $tblname (id int(9) auto_increment not null primary key)");

                if ($query){
                    $this->dbforge->add_field($fields);
                    $this->dbforge->add_column($table, $fields);
                    return "ok";
                }else{
                    return "something went wrong";
                }
            }
            return "column created";

        }catch (Exception $e){
            die ($e->getPrevious());
        }
    }

    private function hlpGetConstraint($type){
        $l['posBracketOpen']  = 0;
        $l['posBracketClose'] = 0;
        $l['posFirstSpace'] = 0;

        $length = strlen($type);
        $l['posBracketOpen']  = strpos($type,"(");
        $l['posBracketClose'] = strpos($type,")");
        $l['posFirstSpace']   = strpos($type," ");

        //  no brackets, no space
        if ($l['posBracketOpen']==false && $l['posFirstSpace']==false){
            $res['Type'] = strtoupper($type);
        }
        // brackets, no space
        if ($l['posBracketOpen'] != false && $l['posFirstSpace'] == false){
            $res['Type'] = strtoupper(substr($type, 0, $l['posBracketOpen']));
            $res['Constraint'] = substr($type,$l['posBracketOpen']+1, $l['posBracketClose']-$l['posBracketOpen']-1);
        }
        // bracket, space
        if ($l['posBracketOpen']!=false &&  $l['posFirstSpace'] != false){
            $res['Type'] = strtoupper(substr($type, 0, $l['posBracketOpen']));
            $res['Constraint'] = substr($type,$l['posBracketOpen']+1, $l['posBracketClose']-$l['posBracketOpen']-1);
            $res['Extra'] = substr($type, $l['posFirstSpace']+1, $length);
        }

        // var_dump($type);
        // var_dump($l);
        // var_dump($res);
        $res['raw'] = $type;
        return $res;
    }


    private function hlpGetmysqlDatatype($payload){
       $r = array();

        $r = (
        array(
            'INT' => array(
                'type'          => 'INT',
                'constraint'    => $payload['txtConstraint'],
                'unsigned'      => $payload['txtUnsigned'] == 'YES' ? TRUE : FALSE,
                'null'          => $payload['txtNull'] == 'YES' ? TRUE : FALSE,
            ),
            'DATE' => array(
                'type'          => 'DATE',
                'null'          => $payload['txtNull'] == 'YES' ? TRUE : FALSE,
            ),
            'VARCHAR'   => array(
                'type'          => 'VARCHAR',
                'constraint'    => $payload['txtConstraint'],
                'null'          => $payload['txtNull'] == 'YES' ?  TRUE : FALSE,
            ),
            'FLOAT' => array(
                'type'          => 'FLOAT',
                'constraint'    => $payload['txtConstraint'],
                'null'          => $payload['txtNull'] == 'YES' ?  TRUE : FALSE,
            ),

            ));


        $res = array(
            $payload['txtName'] => $r[$payload['txtDatatype']]
        );
        return $res;
    }

    private function CreateControllerFromTemplate($modulename){
            $strModulenameLowCase = strtolower($modulename); // make lowcase
            $strModulenameUpCase  = $strModulenameLowCase;  // copy to upcase
            $strModulenameUpCase[0] = strtoupper($strModulenameLowCase[0]); //make upcase[0]

            $tpl = file_get_contents(__DIR__ . "/../models/adminControllerTemplate.txt");
            $tpl = str_replace('<Module>',$strModulenameUpCase, $tpl);
            $tpl = str_replace('<module>', $strModulenameLowCase, $tpl);

            $fullFN = __DIR__ . "/../../" .
                $strModulenameLowCase .
                "/controllers/".
                $strModulenameUpCase.".php";
            file_put_contents($fullFN, $tpl);
            $cmd_chmod =  $this->cmds['cmd_chmod_dir'];
            shell_exec($cmd_chmod.$fullFN);
            return "created_outputfile: " . $fullFN . "<br>";
    }

    private function CreateModelFromTemplate($modulename){
        $strModulenameLowCase = strtolower($modulename); // make lowcase
        $strModulenameUpCase  = $strModulenameLowCase;  // copy to upcase
        $strModulenameUpCase[0] = strtoupper($strModulenameLowCase[0]); //make upcase[0]

        $tpl = file_get_contents(__DIR__ . "/../models/adminModelTemplate.txt");
        $tpl = str_replace('<Module>',$strModulenameUpCase, $tpl);
        $tpl = str_replace('<module>', $strModulenameLowCase, $tpl);

        $fullFN = __DIR__ . "/../../" .
            $strModulenameLowCase .
            "/models/".
            "Mdl_" . $strModulenameLowCase.".php";
        file_put_contents($fullFN, $tpl);
        $cmd_chmod =  $this->cmds['cmd_chmod_dir'];
        shell_exec($cmd_chmod.$fullFN);
        return "created_outputfile: " . $fullFN . "<br>";
    }

    private function CreateViewFromTemplate($modulename){
        $strModulenameLowCase = strtolower($modulename); // make lowcase
        $strModulenameUpCase  = $strModulenameLowCase;  // copy to upcase
        $strModulenameUpCase[0] = strtoupper($strModulenameLowCase[0]); //make upcase[0]

        $tpl = file_get_contents(__DIR__ . "/../models/adminViewTemplate.txt");
        $tpl = str_replace('<Module>',$strModulenameUpCase, $tpl);
        $tpl = str_replace('<module>', $strModulenameLowCase, $tpl);

        $fullFN = __DIR__ . "/../../" .
            $strModulenameLowCase .
            "/views/".
            "view_" . $strModulenameLowCase.".php";
        file_put_contents($fullFN, $tpl);
        $cmd_chmod =  $this->cmds['cmd_chmod_dir'];
        shell_exec($cmd_chmod.$fullFN);
        return "created_outputfile: " . $fullFN . "<br>";
    }



    private function createfolder($modulename){
        $moduleName = $modulename;
        $moduleName = strtolower($modulename);
        $fullPath = FCPATH . "application/modules/";
        $cmd_mkdir =  $this->cmds['cmd_mkdir'];
        $cmd_chmod =  $this->cmds['cmd_chmod_dir'];
        $arrFolder = array(
            'Module'        => $fullPath . $moduleName,
            'Controller'    => $fullPath . $moduleName . '/controllers',
            'Models'        => $fullPath . $moduleName . '/models',
            'Views'         => $fullPath . $moduleName . '/views',
        );
        $res ="";
        foreach ($arrFolder as $k=>$v){

            if (!file_exists($v)){
                $res .= "$k ...";
                if (!shell_exec($cmd_mkdir.$v)) { $res .= "mkdir : ok<br>";} else { die("Error on creating folders<br>"); }
            }
            if (file_exists($v)){
                $res .= "$k ...";
                if (!shell_exec($cmd_chmod.$v)) { $res .= "chown : ok<br>";} else { die("Error on chmod folders<br>"); }
            }

        }
        return $res;
    }
}