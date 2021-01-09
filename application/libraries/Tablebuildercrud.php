<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tablebuildercrud {

    private $CI;
    private $rawData;   // set by app to be worked on by this formbuilder
                        // array('structure' => xyxy;  and 'data'=> xxx)
    private $crudConfig;// additional data for preparing ajax funcionality

    function __construct(){
        $this->CI =& get_instance();
        $this->crudConfig = null;
    }

    public function setCRUDTableData($data,$CRUDConfig=null){
        if (!isset($data)){ return false;}else{
            $this->rawData = $data;
        }
        if (isset($CRUDConfig)){
            $this->crudConfig = $CRUDConfig;
            /*
             * $Config = array(
            'module' => $this->data['module'],   // mandatory
            'renameFields' => array(             // optional
                'bezeichner' => 'Bezeichnung',
                'beschreibung' => 'Was des auch isch',
                'kosten' => 'Kosten',
                'zeiteinheit' => 'Zeiteinheit'
            ),
            'supressFields' => array(           // optional
                'id' => 'id'
            )
        );*/
        }else{
            die("you need to set a config in tablebuilder 21 tablebuildercrud.php");
        }
    }

    public function getCRUDTableHTML(){
        if (!isset($this->rawData)){
            die("getTableHTML cannot be invoked, before setTableData hasn not received data; 24 Tablebuildercrud.php");
        }
        // generate thead and remember column-names
        $thColLookup=array(); $thColCounter=0;
        if ($this->crudConfig['allowRowDelete']){$firstrun=true;}
        $thead = "<tr>"; foreach ($this->rawData['structure'] as $h=>$v){ //only using keys ...

                if (isset($this->crudConfig['supressFields'][$h])){
                    $thColLookup[$thColCounter]     = $h; $thColCounter++;    // supress field
                    continue;
                }else{
                    if ($this->crudConfig['allowRowDelete']){if ($firstrun){ $thead.="<th></th>";
                                    $thColLookup[$thColCounter]     = $h; $thColCounter++;
                                    $firstrun=false;}} // put delete button
                    $thColLookup[$thColCounter]     = $h; $thColCounter++;
                    if (isset($this->crudConfig['renameFields'])){         // rename column header if set in Config
                        if (isset($this->crudConfig['renameFields'][$h])){
                            $thead.= "<th>".$this->crudConfig['renameFields'][$h]."</th>";
                        }else{
                            $thead.= "<th>$h</th>";
                        }
                    }else{
                        $thead.= "<th>$h</th>";
                    }
                }
        }$thead.="</tr>";

        $tdata = "";
        $tmpl  = ""; $tmplID=""; $tmplArrID=array(); $tmplFinished=false; // Template string to be output separate
        // generate tbody and set id = row_id and columns names

        foreach ($this->rawData['data'] as $td){ // td is object... therefore $td->id...
            if ($this->crudConfig['allowRowDelete']){$firstrun=true;}
            $tdColCounter=0;
            $tdata.="<tr>";
            if (!$tmplFinished){

                $tmpl.="<tr id=\"newrow\">";
                if ($this->crudConfig['allowRowDelete']){ if ($firstrun){$tmpl.="<td id=\"0-\"></td>";}  }

            }

            foreach ($td as $k=>$t){

                if (isset($this->crudConfig['supressFields'][$k])){       // supress field
                    $tdColCounter++;
                    continue;
                }else{
                    if ($this->crudConfig['allowRowDelete']){if ($firstrun){
                        $tdata.="<td id=\"".$td->id."\">".$this->genDelButton($td->id, $this->crudConfig['module'])."</td>";$tdColCounter++;
                        $firstrun=false;
                    } }// put delete button
                    $tdata.="<td ";
                    if (!$tmplFinished){
                        if ($this->crudConfig['allowRowDelete']){  if ($firstrun){$tmpl.="<td></td>";}}
                        $tmpl .="<td ";}
                    if (isset($this->crudConfig['module'])){
                        $tdata.="id=".$td->id."-".$thColLookup[$tdColCounter]."-".$this->crudConfig['module']." ";
                        $tmpl .="id="."0"."-".$thColLookup[$tdColCounter]."-".$this->crudConfig['module']." ";
                        $tmplID="0"."-".$thColLookup[$tdColCounter]."-".$this->crudConfig['module'];
                    }
                    $tdata.=">$t";
                    $tmpl .=">";
                    if (!$tmplFinished){$tmpl .=">  ";}
                    $tdata.="</td>";
                    if (!$tmplFinished){$tmpl .="</td>"; array_push($tmplArrID, $tmplID); $tmplID="";}
                    $tdColCounter++;
                }
            }
            $tdata.="</tr>";
            if (!$tmplFinished){$tmpl .="</tr>";  $tmplFinished=true;}

        };
        // write out table for returning ...
        $r = "";
        if ($this->crudConfig['enableNewEntry']){
           $r.= $this->genAnchorADDRow();
        }
        $r.= '<table id="myTable" '; if (isset($this->crudConfig['module'])){$r.='id="'.$this->crudConfig['module'].'" ';}
        $r.='class="table is-bordered">
               <thead>';
        $r.= $thead;
        $r.= ' </thead>
               <tbody>';
        $r.=$tdata;
        $r.='</tbody>
        </table>';

        $data = array(
            'tablestring' => $r,
            'newRowTemplate' => $tmpl,
            'newRowTemplateIDs' => $tmplArrID
        );
        return $data;
    }
    private function genAnchorADDRow(){
        // $this->crudConfig['module']
       return '<a href="#" onclick="insertRowForEdit();">Eintrag hinzuf&uuml;gen</a>';

    }
    private function genDelButton($id,$module){
        return '<button id="'.$id.'-'.$module.'" onclick="deleteRow(this);">del</button>';
    }

}