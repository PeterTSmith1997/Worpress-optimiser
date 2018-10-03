<?php
/**
* This script is designed to optimise the core tables used in Wordpress
* This script is designed to be used as a cron job, 
* to use this script enter your email address between the quotation marks beside the email 
* If your Wordpress install is in a sub directory enter the directory on lines 15 and 16 before the /wp-config.php and /wp-blog-header.php
* for example, if your Wordpress install is in a folder called 'blog' change lines to become /blog/wp-config.php etc
*/


//******************************** EMAIL DETAILS*******************************************
$email ='email.here@domain.com';
//******************************************************************************************
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root.'/wp-config.php';
require $root.'/wp-blog-header.php';

function getTablepre(){
    global $wpdb;
    $tablePre = $wpdb->prefix;
    return $tablePre;
}

function setsql(){
    if (getTablepre() == null){
        return "OPTIMIZE TABLE `commentmeta`, `comments`,
        `links`, `options`, `postmeta`, `posts`, `termmeta`,
        `terms`, 'term_relationships`, `term_taxonomy`, `usermeta`, `users`";
    }
    else{
        $pre = getTablepre();
    
        return "OPTIMIZE TABLE " .$pre."commentmeta, " . $pre. "comments, ".
                $pre."links, ". $pre. "options, ". $pre."postmeta, ". $pre."posts, " .$pre."termmeta, ". $pre."terms, ".$pre."term_relationships, ". $pre."term_taxonomy, ". $pre."usermeta, ".$pre."users;";
    }
}
function MailNotice($subject, $message){
    global $email;
    $message = wordwrap($message, 70, "\r\n");
    if (!mail($email, $subject, $message)){
        error_log("WP Optimiser ubable to send meassge". $message);
    }
}
try {
    $dbh = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = setsql();
    if ($dbh->exec($sql) === 0) {
        $message = "Tables optimised";
        MailNotice('WP notice', $message);
    }
}

catch (PDOException $e){
    $message = "Error message was" . $e->getMessage();
    MailNotice('WP error', $message);
}
