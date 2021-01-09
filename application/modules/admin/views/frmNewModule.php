<h1>Create new Module </h1>
<p>Complete the Form, please.</p>
<?php echo validation_errors(); ?>
<?php echo form_open('/admin/mngModules'); ?>
<h5>Module Name</h5>
<input type="text" name="modulename" value="<?php echo set_value('modulename'); ?>" size="50" />
<div><input type="submit" value="Submit" /></div>

<hr>
<h1>Available Modules</h1>
<?php
    $mdls = modules::run('admin/mngGetModelsFromDir');
    if (sizeof($mdls) == 0){
        echo "no modules found";
    }else{
        echo "<table class=\"table is-bordered\">";
        foreach ($mdls as $m){
            echo "<tr><td>".anchor("/$m",$m)."</td></tr>";
        }
        echo "</table>";

    }
?>

