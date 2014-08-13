<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once('includes/krumo/class.krumo.php');
require_once('includes/class.moodlequery.php');
require_once('config.php'); // edited config file from moodle install
?>

<h1>Example</h1>
<p>First we get the moodle session id from browser cookie and look up the student</p>

<?php
$moodle = new MoodleQuery($CFG);

if (isset($_COOKIE['MoodleSession'])) {
  echo '<p><em>Moodle session id found</em></p>';
  // $student = $moodle->getuser(3); // get a different user based on their moodle user id
  if ($student = $moodle->getuser($_COOKIE['MoodleSession'])) { 
    $fullname = '<strong>'. $student->firstname .' '. $student->lastname .'</strong>';
    $courses = $moodle->getenrolments($student);
    echo "<p>You are logged in as student $fullname:</p>";
    krumo($student);
    echo "<p>$fullname is enrolled on the following courses:</p>";
    echo '<dl>';
    foreach ($courses as $course) {
      echo '<dt><a href="'.$course->url.'">'. $course->fullname.' ('.$course->idnumber.')</a></dt>';
      echo ($course->summary) ? '<dd>'. strip_tags($course->summary) .'</dd>' : '';
    }
    echo '</dl>';
    krumo($courses);
  } else {
    echo '<p>No moodle user/session was found.</p>';
    echo '<p>Are you logged into moodle?</p>';
  }
}
else {
 echo '<p><em>Moodle session id <strong>not found</strong></em</p>';
}









//krumo(student_get_course($student['id']));