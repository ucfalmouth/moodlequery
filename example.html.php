<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once('includes/krumo/class.krumo.php');
require_once('includes/class.moodlequery.php');
require_once('config.php');


echo "<h1>API call to get moodle data</h1>";

// $student = get_mdl_user();
// krumo($student);

$moodle = new MoodleQuery($CFG);
// $student = $moodle->getuser($_COOKIE['MoodleSession']);
$student = $moodle->getuser(3);

$courses = $moodle->getenrolments($student);
echo 'list courses';
echo '<dl>';
foreach ($courses as $course) {
  echo '<dt><a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->courseid.'">'. $course->fullname.' ('.$course->idnumber.')</a></dt>';
  echo ($course->summary) ? '<dd>'. strip_tags($course->summary) .'</dd>' : '';
}
echo '</dl>';

echo 'objects';
krumo($student); 
krumo($courses); 

//krumo(student_get_course($student['id']));