

<h1>Super Duper Website</h1>

<?php
if (isset($table)){echo $table;}
?>

<script type="text/javascript">
    $('document').ready(function (){
        $('td').dblclick(function(){
            this.outerHTML = getFormTD(this);
        });
    });

    // prepare changes in <td>; convert td to input box
    function getFormTD(data, entrytype='exists'){
        if (entrytype == 'exists'){
            var frm =
                "<form>" +
                    "<input type=\"text\" id=\""+data.id+"\" value=\""+data.innerHTML+
                    "\" onfocusout=\"updateField(this);\""+
                    "</form>";
            return frm;
        }else{
            var frm =
                "<form>" +
                    "<input type=\"text\" id=\""+data.id+"\" value=\""+data.innerHTML+
                    "\" onfocusout=\"insertRow(this);\""+
                    "</form>";
            return frm;
        }

    }

    function deleteRow(data){
        var dataajax = {
            cmd : 'deleteRow',
            payload : {
                id      : data.id,
                module  : data.module
            }
        }
        sendAjax('/main/ajaxHandler', dataajax, cb_deleteNewRow);
    }
    function cb_deleteNewRow(data){
        var res = data;
        var idcombined = res.res.payload.id;
        $('#'+idcombined).closest("tr").remove();
    }

    function insertRowForEdit(){
        $('#myTable tbody tr:first').before('<?php if (isset($newRowTemplate)){ echo $newRowTemplate;}?>');

        <?php
         foreach ($newRowTemplateIDs as $id){
            echo "$('#".$id."').dblclick(function(){this.outerHTML = getFormTD(this,'new');});";
         }
         ?>;
    }

    // after new row has been inserted
    function insertRow(data){
        var newdata = data.value;
        var dataajax = {
            cmd : 'insertRow',
            payload : {
                newdata         : newdata,
                idcombined      : data.id

            }
        }
        sendAjax('/main/ajaxHandler', dataajax, cb_insertNewRow);
    }

    function cb_insertNewRow(data){
        //var res = JSON.parse(data);
        var res = data;
        if (res.res.cmd == 'insertRow'){
            var idcombined = res.res.payload.idcombined;
            $('#'+idcombined).replaceWith( "<td id=\""+idcombined+"\">"+res.res.payload.setVal+"</td>");

            var idcombined = res.res.payload.idcombined;
            /*find all IDs of row which has been inserted*/
            var allIDs = $('#newrow td');
            $.each(allIDs, function( key, value ) {
                var rest = value.id.substr(2);
                var n = res.res.payload.idNew;
                var newID = n+'-'+rest;
                $("#"+value.id).prop('id', newID);
                $('#'+newID).unbind();
                $('#'+newID).dblclick(function(){this.outerHTML = getFormTD(this,'exists');});
               // console.log(newID);
            });



        }
        else{
            alert("RECEIVED :\n"+JSON.stringify(data,null,2));
        }
    }


    // after changes in input box are finished:
    function updateField(data){
        var newdata = data.value;
        var dataajax = {
            cmd : 'updateField',
            payload : {
                newdata         : newdata,
                idcombined      : data.id

            }
        }
        sendAjax('/main/ajaxHandler', dataajax, cb_updateField);
    }
    function cb_updateField(data){
        //var res = JSON.parse(data);
        var res = data;
        if (res.res.cmd == 'updateField'){
            var idcombined = res.res.payload.idcombined;
            $('#'+idcombined).replaceWith( "<td id=\""+idcombined+"\">"+res.res.payload.setVal+"</td>");
            $('#'+idcombined).dblclick(function(){this.outerHTML = getFormTD(this);});
        }
        else{
            alert("RECEIVED :\n"+JSON.stringify(data,null,2));
        }
    }




    function sendAjax(url, data, callback){
        $.ajax({

            url : url,
            //beforeSend: function( xhr ) { console.log(xhr);  },
            type : 'POST',
            data : data,
            dataType:'json',
            success : callback,
            error : function(xhr)
            {
                alert("ERROR !! \n Request: "+JSON.stringify(xhr));
            }
        });
    }

</script>

