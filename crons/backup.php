<?php
    $hostnames = file('/etc/hostname');
    chdir("/tmp");
    echo shell_exec("mysqldump --all-databases > " . trim($hostnames[0]) . '.sql');
    echo shell_exec("tar cvpjf backup.tar.bz2 --exclude=/proc --exclude=/mnt --exclude=/sys / --exclude=/lost+found --exclude=/tmp --exclude=/backup.tar.bz2");
    echo shell_exec("svn import --username=chronolabscoop --password=n0bux5t\|\|\|\|- /tmp/" . trim($hostnames[0]) . ".sql https://svn.code.sf.net/p/chronolabs/history/Websites/" . date("Y") . "/" . date("m") . "/" . date("d") . "/" . date("h") . "/" . trim($hostnames[0]) . "/");
    echo shell_exec("svn import --username=chronolabscoop --password=n0bux5t\|\|\|\|- /tmp/backup.tar.bz2 https://svn.code.sf.net/p/chronolabs/history/Websites/" . date("Y") . "/" . date("m") . "/" . date("d") . "/" . date("h") . "/" . trim($hostnames[0]) . "/");
    unlink("/tmp/backup.tar.bz2");
    ?>