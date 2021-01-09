$( document ).ready(function() {
    $('.btnCmds').click(function(){
        var txtModulename = $('#selModule option:selected').val()
        var txtName     =   $('#txtName').val();
        var txtTmpName  =   $('#tmpName').val();
        var txtDatatype =   $('#txtDatatype option:selected').val();
        var txtConstraint = $('#txtConstraint').val();
        var txtNull     =   $('#txtNull').val();
        var txtUnsigned =    $('#txtUnsigned option:selected').val();

        var btncmd = this.id;
        var data = {
            cmd : btncmd,
            payload : {
                txtModulename : txtModulename,
                txtName       : txtName,
                tmpName       : txtTmpName,
                txtDatatype   : txtDatatype,
                txtConstraint : txtConstraint,
                txtUnsigned   : txtUnsigned,
                txtNull       : txtNull
            }
        }
        sendAjax('/admin/ajaxHandler', data, cb_cmd_cmd_write2db);

    });

    // intitial
    listfields();

});


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

// ********* ajax response handler "write2db" ****************************
function cb_cmd_cmd_write2db(data){
    //var res = JSON.parse(data);
    var res = data;
    if (res.cmd == 'showListFields'){
        buildHtmlTable('#FieldTable', res.payload);
    }else if (res.cmd == 'cmd_updatefields'){
        //alert(JSON.stringify(res.payload));
        listfields();
    }
    else{
        listfields();
        //alert("RECEIVED :\n"+JSON.stringify(data,null,2));
    }
}

function listfields(){
    var data = this;
    var data = {
        cmd : 'cmd_listfields',
        payload : {
            txtModulename : $('#selModule option:selected').val()
        }
    }
    sendAjax('/admin/ajaxHandler', data, cb_cmd_listfields);
}
function cb_cmd_listfields(data){
    buildHtmlTable('#FieldTable', data);

}
// ******** showListFields ****JS helper functions***************************/
// Builds the HTML Table out of myList.
// https://stackoverflow.com/questions/5180382/convert-json-data-to-a-html-table

    function buildHtmlTable(selector, data) {
        if (Array.isArray(data.payload)){
            var myList = data.payload;
            $(selector).empty();
            $(selector).append("<h1>Structure of Module/Table: "+data.txtModulename+"</h1>");
            var columns = addAllColumnHeaders(myList, selector);

            for (var i = 0; i < myList.length; i++) {
                var row$ = $('<tr/>');
                for (var colIndex = 0; colIndex < columns.length; colIndex++) {
                    var cellValue = myList[i][columns[colIndex]];
                    if (cellValue == null) cellValue = "";
                    row$.append($('<td/>').html(cellValue));
                }
                row$.append('<td><button onclick="send2form(\''+myList[i][columns[0]]+'\');">modify</button></td>'+
                    '<td><button onclick="deleteField(\''+myList[i][columns[0]]+'\');">delete</button></td>'
                );
                $(selector).append(row$);
            }
        }else{
            $(selector).empty();
            $(selector).append("<h2>"+data.payload+"</h2>");
        }



}

// Adds a header row to the table and returns the set of columns.
// Need to do union of keys from all records as some records may not contain
// all records.
function addAllColumnHeaders(myList, selector) {
    var columnSet = [];
    var headerTr$ = $('<tr/>');

    for (var i = 0; i < myList.length; i++) {
        var rowHash = myList[i];
        for (var key in rowHash) {
            if ($.inArray(key, columnSet) == -1) {
                columnSet.push(key);
                headerTr$.append($('<th/>').html(key));
            }
        }
    }
    $(selector).append(headerTr$);

    return columnSet;
}

// ******** showListFields **END*******************************************/


// ******** MODIFY FIELDS *****************************************/
function send2form(fieldname){
    var data = this;
    var data = {
        cmd : 'cmd_btnGetFieldInfo',
        payload : {
            fieldname : fieldname,
            txtModulename : $('#selModule option:selected').val()
        }
    }
    $('#tmpName').val(fieldname);
    sendAjax('/admin/ajaxHandler', data, cb_cmd_send2formHndl);
}
function cb_cmd_send2formHndl(data){

    $('#txtName').val(data.res.payload.Field);
    $('#txtDatatype').val(data.res.payload.Type);
    $('#txtConstraint').val(data.res.payload.Constraint);
    $('#txtNull').val(data.res.payload.Null);
    $('#txtUnsigned').val(data.res.payload.Unsigned);

    //alert("RECEIVED :\n"+JSON.stringify(data,null,2));

}


// ******** MODIFY FIELDS ******** END  ****************************/

// ******** DELETE FIELDS *****************************************/
function deleteField(fieldname){
    var data = this;
    var data = {
        cmd : 'cmd_btnDeleteField',
        payload : {
            fieldname : fieldname,
            txtModulename : $('#selModule option:selected').val()
        }
    }
    sendAjax('/admin/ajaxHandler', data, cb_cmd_deleteFieldHndl);
}
function cb_cmd_deleteFieldHndl(data){
    listfields();
}
// ******** DELETE FIELDS ******** END  ****************************/