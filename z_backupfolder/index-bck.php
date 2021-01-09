<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>diamond grinder</title>
    <link rel="stylesheet" href="../css/bulma/css/bulma.css" />
    <link rel="stylesheet" href="css/diacut.css" />

    <link rel="icon" href="/images/icons/diamond.ico" />
</head>

<body>
<div class="container">
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a class="navbar-item" href="#">
                <img src="/images/icons/diamond.ico" width="28" height="28">
            </a>

            <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>

        <div id="navbarBasicExample"   class="navbar-menu">
            <div class="navbar-start">
                <!--<a href="/backup"   class="navbar-item">Backup-darkstar</a> -->
                <div class="navbar-item has-dropdown is-hoverable">
                    <a href="/backup/dashboard" class="navbar-link">Backup Darkstar</a>
                    <div class="navbar-dropdown">
                        <a href="/backup/dashboard" class="navbar-item">Dashboard</a><hr class="navbar-divider">
                        <a href="/backup/backdef" class="navbar-item">Define Backupsets</a><hr class="navbar-divider">
                        <a href="/backup/backctrl" class="navbar-item">Run Backups</a><hr class="navbar-divider">
                        <a href="/backup/restFiles"class="navbar-item">Restore Files</a><hr class="navbar-divider">
                        <a href="/backup/showHistory" class="navbar-item">Show Backup History</a><hr class="navbar-divider">
                    </div>
                </div>
            </div>

            <div class="navbar-end">
                <div class="navbar-item">
                    <div class="buttons">
                        <a class="button is-primary"><strong>Sign up</strong></a>
                        <a class="button is-light"><strong>Login</strong></a>
                        <a href="/admin/dashboard" class="button is-danger">Admin</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <section class="section">
        <div class="container">
            content
        </div>
    </section>

</div>

</body>
</html>
