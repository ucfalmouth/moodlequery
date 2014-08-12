<?php

class MoodleQuery
{
  public $user = NULL;
  private $config = NULL;
  private $mdb = NULL;

  public function __construct($CFG)
  { 
    $this->config = $CFG;
    // override mysqli for PDO
    $this->config->dbtype = ($this->config->dbtype == 'mysqli') ? 'mysql' : $this->config->dbtype;
    $db = new PDO($this->config->dbtype.':host='.''.$this->config->dbhost.';dbname='.$this->config->dbname, "moodle_26", "m768zVWyH3c5Hyez");
    if (is_object($db)) {
      $this->mdb = $db;
      return true;
    } else {
      return false;
    }
  }

  public function getuser($sessionid = NULL)
  {
    if (!is_object($this->user) && $sessionid)  {
      try {
        $query = "SELECT * 
                  FROM mdl_sessions as ms
                  RIGHT JOIN mdl_user as mu 
                  ON ms.userid = mu.id
                  WHERE sid = :msid";
        $stmt = $this->mdb->prepare($query);
        $stmt->execute(array('msid' => $sessionid));
        $result = $stmt->fetchObject();
        if ( count($result) ) { 
          $this->user = $result;
          return $result; 
        } else {
          return false;
        }
      } catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
      }
    } elseif (!$sessionid) {
      return false;
    }

  }
 
  public function getProperty()
  {
      return $this->prop1 . "<br />";
  }
}