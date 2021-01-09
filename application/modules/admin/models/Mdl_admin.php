<?php
class Mdl_admin extends CI_Model{
    private $tblname;


    function __construct(){
        parent::__construct();
        $this->tblname = 'admin';
    }

    // basic CRUD create update delete read stuff
    public function get(){
        $query =  $this->db->get($this->tblname)->result();
        return $query;
    }
    public function getRow($id){
        $this->db->where('id', $id);
        $query = $this->db->get($this->tblname)->row();
        return $query;
    }
    public function get_Where($col, $where, $limit=0, $offset=0){
        $query = $this->db->get_where($this->tblname, array($col => $where), $limit, $offset)->result();
        return $query;
    }
   
    public function getRows(){
        $query = $this->db->get($this->tblname)->result();
        return $query;
    }
    // @TODO: check ob where Bedingungen sinnvoll hier...
    public function updateRow($data){
        // $data = array('id' => "irgendwas", spalten...=> spaltenwerte
        // oder ein pbejkct  stdclass object->id
        $query =$this->db->replace($this->tblname, $data);
        return $query;
    }
    public function insert($data){
        $this->db->set($data);
        $query = $this->db->insert($this->tblname);
        return $query;
    }
    public function delete($col,$where){
        $query = $this->db->delete($this->tblname, array($col => $where));
        return $query;
    }
    public function getTableStruct(){
        $fieldnames =  $this->db->list_fields($this->tblname);
        $ret = array();
        foreach ($fieldnames as $k=>$v){
            $ret[$v] = '';
        }
        return $ret;
    }
    public function get_was_kann_datenbank_an_datentypen(){
        return "das...was si ekann ";
    }
    public function customsqlArr($sqlstring){
        return $this->db->query($sqlstring)->result();
    }
    public function customsqlSGL($sqlstring){
        return $this->db->query($sqlstring)->row();
    }
    // custom model methods einbauen könnne
}