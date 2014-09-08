serverpilot-api-client
======================

PHP Framework for [ServerPilot.io](https://serverpilot.io) API

ServerPilot is a website for creating applications at the click of a button for your websites.  It's automatically configured on the backend, but this will let you use their API to integrate with billing systems, such as WHMCS.

    <?php
    $client_id = '';
    $api_token = '';
    require_once 'ServerPilot.class.php';
    $sp = new ServerPilot($client_id, $api_token);
    print_r($sp->servers);
    print_r($sp->users);
    print_r($sp->apps);
    print_r($sp->databases);
    $sp->server_create($hostname);
    print_r($sp->actions);
    ?>
