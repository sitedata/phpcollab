<?php
return array(
    "please_login" => "Identifique-se",
    "requirements" => "Requisitos do Sistema",
    "login" => "Entrar",

//    "please_login" => "Please log in",
//    "requirements" => "System Requirements",
//    "login" => "Log In",
    "no_items" => "No items to display",
    "logout" => "Log Out",
    "byteUnits" => array(
        0 => 'Bytes',
        1 => 'KB',
        2 => 'MB',
        3 => 'GB'
    ),
    "dayNameArray" => array(1 => "Monday", 2 => "Tuesday", 3 => "Wednesday", 4 => "Thursday", 5 => "Friday", 6 => "Saturday", 7 => "Sunday"),
    "help" => [
        "setup_mkdirMethod" => "If safe-mode is On, you need to set a Ftp account to be able to create folder with file management.",
        "setup_notifications" => "Notificações por email aos usuários:<br/><br/>&nbsp;&nbsp;- Atribuição de tarefa<br/>&nbsp;&nbsp;- Nova mensagem<br/>&nbsp;&nbsp;- Alterações nas tarefas<br/>&nbsp;&nbsp;- ect<br/><br/>Requerem um servidor de SMTP/SendMail válido.",
        "setup_forcedlogin" => "If false, disallow external link with login/password in url",
        "setup_langdefault" => "Choose language to be selected on login by default or leave blank to use auto_detect browser language.",
        "setup_myprefix" => "Set this value if you have tables with same name in existing database.<br/><br/>assignments<br/>bookmarks<br/>bookmarks_categories<br/>calendar<br/>files<br/>logs<br/>members<br/>notes<br/>notifications<br/>organizations<br/>phases<br/>posts<br/>projects<br/>reports<br/>sorting<br/>subtasks<br/>support_posts<br/>support_requests<br/>tasks<br/>teams<br/>topics<br/>updates<br/><br/>Leave blank for not use table prefix.",
        "setup_loginmethod" => "Method to store passwords in database.<br/>Set to &quot,Crypt&quot, in order CVS authentication and htaccess authentification to work (if CVS support and/or htaccess authentification are enabled).",
        "admin_update" => "Respect strictly the order indicated to update your version<br/>1. Edit settings (supplement the new parameters)<br/>2. Edit database (update in agreement with your preceding version)",
        "task_scope_creep" => "Difference in days between due date and complete date (bold if positive)",
        "max_file_size" => "Maximum file size of a file to upload",
        "project_disk_space" => "Total size of files for the project",
        "project_scope_creep" => "Difference in days between due date and complete date (bold if positive). Total for all tasks",
        "mycompany_logo" => "Upload any logo of your company. Appears in header, instead of title site",
        "calendar_shortname" => "Label to appear in monthly calendar view. Mandatory",
        "user_autologout" => "Time in sec. to be disconnected after no activity. 0 to disable",
        "user_timezone" => "Set your GMT timezone",
        //2.4
        "setup_clientsfilter" => "Filter to see only logged user clients",
        "setup_projectsfilter" => "Filter to see only the project when the user are in the team",
        //2.5
        "setup_notificationMethod" => "Set method to send email notifications: with internal php mail function (need for having a smtp server or sendmail configured in the parameters of php) or with a personalized smtp server",
        //2.5 fullo
        "newsdesk_links" => "to add multiple links use semicolon",
    ]
);

//$strings["byteUnits"] = array(0 => 'Bytes', 1 => 'KB', 2 => 'MB', 3=> 'GB');
//$strings["preferences"] = "Preferences";
//$strings["my_tasks"] = "My Tasks";
//
//return $strings;
