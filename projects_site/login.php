<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include "../includes/library.php";

//case session fails
if ($url != "") {
	phpCollab\Util::headerFunction("../login.php?url=$url");
	exit;

//default case
} else {
	phpCollab\Util::headerFunction("../login.php");
	exit;
}
?>