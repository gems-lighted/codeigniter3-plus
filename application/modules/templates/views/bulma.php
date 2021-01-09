<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php  if (isset($addMeta)){echo $addMeta;} ?>
    <title><?= BASE_URL();?></title>
    <link rel="stylesheet" href="/css/bulma/css/bulma.css" />
    <link rel="stylesheet" href="/css/diagrin.css" />
    <?php if (isset($addCSS)){echo $addCSS; }?>
    <link rel="icon" href="/images/icons/diamond.ico" />
    <script type="text/javascript" src="/js/jquery.js"></script>

    <?php if (isset($addJSlibs)){echo $addJSlibs; }?>
    <script type="text/javascript" src="/js/bulma.js"></script>

</head>

<body>
<div id="wrapper">
    <div class="container">
        <nav class="navbar" role="navigation" aria-label="main navigation">
            <div class="navbar-brand">
                <a class="navbar-item" href="/main/index">
                    <img src="/images/icons/diamond.ico" width="28" height="28">
                </a>

                <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbar">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>
            </div>

            <div id="navbar-menu"   class="navbar-menu">
                <div class="navbar-start">
                    <!--   <div class="navbar-item has-dropdown is-hoverable">
                           <a href="/backup/dashboard" class="navbar-link">Backup Darkstar</a>
                           <div class="navbar-dropdown">
                               <a href="/backup/dashboard" class="navbar-item">Dashboard</a><hr class="navbar-divider">
                               <a href="/backup/backdef" class="navbar-item">Define Backupsets</a><hr class="navbar-divider">
                               <a href="/backup/backctrl" class="navbar-item">Run Backups</a><hr class="navbar-divider">
                               <a href="/backup/restFiles"class="navbar-item">Restore Files</a><hr class="navbar-divider">
                               <a href="/backup/showHistory" class="navbar-item">Show Backup History</a><hr class="navbar-divider">
                           </div>
                       </div>
                -->
                </div>

                <div class="navbar-end">
                    <div class="navbar-item has-dropdown is-hoverable">
                        <a id="admin" href="#" class="navbar-link">Admin</a>
                        <div class="navbar-dropdown">
                            <a id="mngModules" href="/admin/mngModules" class="navbar-item">Mng Modules</a><hr class="navbar-divider">
                            <a id="mngModels"  href="/admin/mngModels" class="navbar-item">Mng Models</a><hr class="navbar-divider">
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <section class="section">
            <div class="container">
                <?php
                if (isset($module) & isset($view_file)){$this->load->view($module . '/' . $view_file);}
                else {echo ' kein Aufruf    ';}
                ?>
            </div>
        </section>

    </div>

</div>
<footer class="footer">
    Footer....stuff ....
</footer>
</body>

</html>
