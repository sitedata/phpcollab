<?php
#Application name: PhpCollab
#Status page: 0

// Navigation Start
echo <<<NAVIGATION
<form method="post" name="login"
      action="{$pathMantis}login.php?url=http://{$request->server->get("HTTP_HOST")}{$request->server->get("REQUEST_URI")}&id={$projectSession}">
    <input type="hidden" name="username" value="{$loginSession}">
    <input type="hidden" name="password" value="{$passwordSession}">
NAVIGATION;
