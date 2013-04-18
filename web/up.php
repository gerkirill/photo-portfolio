<?php
ignore_user_abort(true);
ob_implicit_flush(true);
set_time_limit(0);
chdir(dirname(__DIR__));
$commands = array(
        'git pull',
        './composer.phar install',
        'app/console -n doctrine:schema:update --force',
        'app/console -n cache:clear',
        'app/console -n cache:clear --env=prod',
        'app/console -n assetic:dump',
        'app/console -n assets:install',
        'app/console -n doctrine:migrations:migrate'
);
foreach($commands as $command) {
        echo 'Execute: '.$command."\n\n";
        echo `$command`;
        echo "\n-------------------------------------------------------------\n";
}
echo "\n";