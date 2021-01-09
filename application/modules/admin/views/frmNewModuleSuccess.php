<h1>Created Files / Folders</h1>


<ul>
    <li><?php echo anchor('/admin/mngModules','Add another one'); ?></li>
    <li><?php echo anchor('/'.$newmodulename,'Open new module'.$newmodulename); ?></li>
</ul>




<?php
echo "<h2>Success. Check directory tree on file system.</h2><br><br>";
echo "<p>$log</p>";//echo $log;
?>


<style>
    p {
        padding: 1em;
        border: 1px solid black;
        font-size: xx-small;
    }
</style>

