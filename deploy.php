<?php

namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/php-fpm.php';
require 'contrib/npm.php';

set('application', 'websocket');
set('repository', 'git@github.com:stuntrocket/websocket.git');
set('php_fpm_version', '7.4');

host('staging')
  ->set('remote_user', 'pi')
  ->set('hostname', 'naspi')
  ->set('branch', 'master')
  ->set('http_user', 'pi')
  ->set('deploy_path', '/var/www/vhosts/{{application}}');

host('production')
    ->set('remote_user', 'deployer')
    ->set('hostname', 'pusher.venuebookingsite.info')
    ->set('branch', 'master')
    ->set('http_user', 'deployer')
    ->set('deploy_path', '/var/www/vhosts/{{hostname}}');

task('deploy', [
  'deploy:prepare',
  'deploy:vendors',
//  'artisan:storage:link',
  'artisan:view:cache',
  'artisan:config:cache',
  'artisan:migrate',
  'deploy:publish',
  'php-fpm:reload',
  'artisan:queue:restart'
]);

task('npm:run:prod', function () {
    cd('{{release_or_current_path}}');

    run('php artisan nova:install');
    run('php artisan migrate');
    run('php artisan nova:publish');
});

after('deploy:failed', 'deploy:unlock');
