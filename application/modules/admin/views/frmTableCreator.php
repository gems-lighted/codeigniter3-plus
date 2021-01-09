<?php
if (empty($knownModules)){
    echo "<h1>no modules created yet - ";
    echo anchor("/admin/mngModules", "Go here"); echo " for creating some";
    die();
}else{
    ?>
<h1>Manage Table for Module:  <select onchange="listfields();" id="selModule">
<?php
foreach ($knownModules as $m) { echo '<option id="'.$m.'">'.$m.'</option>';}
echo " </select></h1>";
}?>

<div class="columns">
    <div class="column is-one-quarter">

        <hr><div id="fielddefinition">
            <table class="is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                <tr><td>fieldname</td><td>  <input type="text" id="txtName" value="" size="9" /></td></tr>

                <tr><td>Type</td><td><select id="txtDatatype">
                            <option>VARCHAR</option>
                            <option>DATE</option>
                            <option>INT</option>
                            <option>FLOAT</option>
                        </select></td></tr>
                <tr><td>constrain</td><td><input type="text" id="txtConstraint" value="" size="9" /></td></tr>
                <tr><td>unsigned (int)</td><td><select id="txtUnsigned"><option>YES</option><option>NO</option></select></td></tr>
                <tr><td>null allowed</td><td><select id="txtNull"><option>YES</option><option>NO</option></select></td></tr>
                <input type="hidden" id="tmpName" />
            </table>
            <table>
                <tr><td><button class="btnCmds" id="cmd_write2db">Add field</button></td>
                    <td><button class="btnCmds" id="cmd_updatefields">Update Field</button></td>
                </tr>
            </table>
        </div>

    </div>
    <div class="column is-three-quarter">

        <div id="fieldtable">
            <table id="FieldTable" class="table  is-hoverable is-bordered is-narrow" style="margin-top: 1em;">
            </table>
        </div>

        <hr>
    </div>
</div>

<style>
    .columns {
        margin-top: 4em;
        border: 1px solid grey;
    }
    table tr td {
        padding-left: 1em;
    }
    #fieldtable {
        margin-left: 3em;
    }


</style>
