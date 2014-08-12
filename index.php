<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once('includes/moodle_queries.php');
require_once('includes/krumo/class.krumo.php');
require_once('includes/class.moodlequery.php');
require_once('config.php');


echo "<h1>API call to get moodle data</h1>";

// $student = get_mdl_user();
// krumo($student);

$moodle = new MoodleQuery($CFG);
$student = $moodle->getuser($_COOKIE['MoodleSession']);

krumo($student); 

//krumo(student_get_course($student['id']));