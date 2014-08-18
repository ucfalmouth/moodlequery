<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once('includes/krumo/class.krumo.php');
require_once('class.moodlequery.php');
require_once('class.aspireapi.php');
require_once('config.php'); // edited config file from moodle install

echo '<h1>Example</h1><br/>';
echo '<h2>Moodle</h2>';
echo '<p>First we get the moodle session id from browser cookie and look up the student</p>';

if (isset($_COOKIE['MoodleSession'])) {

  echo '<p><em>Moodle session id found</em></p>';
  // create a connection to the moodle db
  $moodle = new MoodleQuery($CFG);

  // get student from moodle session (or optionally from moodle student id) 
  if ($student = $moodle->getuser($_COOKIE['MoodleSession'])) { 

    // student details
    $fullname = '<strong>'. $student->firstname .' '. $student->lastname .'</strong>';
    echo "<p>You are logged in as student $fullname:</p>";
    krumo($student);

    // course details
    $courses = $moodle->getenrolments($student);
    echo "<p>$fullname is enrolled on the following courses:</p>";
    echo '<dl>';
    foreach ($courses as $course) {
      echo '<dt><a href="'.$course->url.'">'. $course->fullname.' ('.$course->idnumber.')</a></dt>';
      echo ($course->summary) ? '<dd>'. strip_tags($course->summary) .'</dd>' : '';
    }
    echo '</dl>';
    krumo($courses);

    // (aspire) reading list information
    echo '<br/><h2>Talis Aspire</h2>';
    if ($aspireconfig = $moodle->getaspireconfig()){
      echo '<p>The following configuration was found within moodle for aspire reading lists:</p>';
      krumo($aspireconfig);
      $aspire = new AspireAPI($aspireconfig);
      echo '<p>The following reading lists in Aspire match your enrolled modules</p>';
 
      foreach($courses as $course) {
        $readinglists = $aspire->modulelists($course);
        echo '<ul>';
        foreach($readinglists as $rl) {
          echo '<li>'.$rl['html'].'</li>';
        }
        echo '</ul>';
        krumo($readinglists);
      }
    }
    else {
      echo '<p>No aspire information was found in moodle.</p>';
      echo '<p>Is there a Talis Aspire moodle plugin installed?</p>';
    }

  }
  else {
    echo '<p>No moodle user/session was found.</p>';
    echo '<p>Are you logged into moodle?</p>';
  }
}
else {
 echo '<p><em>Moodle session id <strong>not found</strong></em</p>';
}









//krumo(student_get_course($student['id']));