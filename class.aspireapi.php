<?php

class AspireAPI
{
  private $config = NULL;
  private $aspireurl = NULL;
  private $target = NULL; // level of aspire hierarchy (eg module)

  public function __construct($mdl_config_plugins)
  { 
    if (is_array($mdl_config_plugins)) {
      // $this->config = $mdl_config_plugins;
      if (isset($mdl_config_plugins['aspirelists'])) {
        $this->aspireurl = $mdl_config_plugins['aspirelists']['targetAspire'];
        $this->target = $mdl_config_plugins['aspirelists']['targetKG'];
        return true;
      } 
      elseif (isset($mdl_config_plugins['mod_aspirelists'])) {
        $this->aspireurl = $mdl_config_plugins['mod_aspirelists']['targetAspire'];
        $this->target = $mdl_config_plugins['mod_aspirelists']['targetKG'];
        $this->config = $mdl_config_plugins['mod_aspirelists']; // other config, eg time periods
        return true;
      }
    }
    return false;
  }

  /* reading lists per course/module
   *
   *
   */
  public function modulelists($course) {
    if (is_object($course)) 
    {
      $code = $course->idnumber;
      $site = $this->aspireurl;
      $targetKG = $this->target;
      // following code forked from aspire block plugin for moodle (2011)
      // https://code.google.com/p/aspire-moodle-integration/
      if ($code)
      {
        $code = strtolower($code);  
        $url = "$site/$targetKG/$code/lists.json";
        $ch = curl_init();
        $options = array(
            CURLOPT_URL            => $url,
            CURLOPT_HEADER         => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_HTTP_VERSION      => CURL_HTTP_VERSION_1_1
        );
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);

        $output = '';
        if ($response) // if valid response from curl:
        {
          $data = json_decode($response,true); // decode the returned JSON data
          if(isset($data["$site/$targetKG/$code"]) && isset($data["$site/$targetKG/$code"]['http://purl.org/vocab/resourcelist/schema#usesList'])) // if there are any lists...
          {
            $lists = array();
            foreach ($data["$site/$targetKG/$code"]['http://purl.org/vocab/resourcelist/schema#usesList'] as $usesList) // for each list this module uses...
            {
              $list = array();
              $list["url"] = $usesList["value"]; // extract the list URL
              $list["name"] = $data[$list["url"]]['http://rdfs.org/sioc/spec/name'][0]['value']; // extract the list name
              // get last updated date
              if (isset($data[$list["url"]]['http://purl.org/vocab/resourcelist/schema#lastUpdated'])) // if there is a last updated date...
              {
                // set up timezone 
                date_default_timezone_set('Europe/London');

                // extract date in human readable format...
                $list['lastUpdatedDate'] = date('l j F Y',
                    strtotime($data[$list["url"]]['http://purl.org/vocab/resourcelist/schema#lastUpdated'][0]['value'])); 
              }
              // count the number of items
              $itemCount = 0; 
              if (isset($data[$list["url"]]['http://purl.org/vocab/resourcelist/schema#contains'])) // if the list contains anything...
              {
                foreach ($data[$list["url"]]['http://purl.org/vocab/resourcelist/schema#contains'] as $things) // loop through the list of things the list contains...
                {
                  if (preg_match('/\/items\//',$things['value'])) // if the thing is an item, incrememt the item count (lists can contain sections, too)
                  {
                    $itemCount++; 
                  }
                }
              }
              $list['count'] = $itemCount;
              array_push($lists,$list);
            }
            // usort($lists,'sortByName');
            // pre-render some example html for lists 
            foreach ($lists as $key => $list)
            {
              $itemNoun = ($list['count'] == 1) ? "item" : "items";
              $output = "<a href='".$list['url']."'>".$list['name']."</a>";
              $output .= ($list['count'] > 0) ? " (".$list['count']." $itemNoun)" : '';
              $output .= (isset($list["lastUpdatedDate"])) ? ', last updated '.$this->contextualTime(strtotime($list["lastUpdatedDate"])) : '';
              $lists[$key]['html'] = $output;
            }
            return $lists;
          }
        }
      }
    }
    return false;
  }

  // taken directly from aspire block plugin
  private function contextualTime($small_ts, $large_ts=false) {
    if(!$large_ts) $large_ts = time();
    $n = $large_ts - $small_ts;
    if($n <= 1) return 'less than 1 second ago';
    if($n < (60)) return $n . ' seconds ago';
    if($n < (60*60)) { $minutes = round($n/60); return 'about ' . $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago'; }
    if($n < (60*60*16)) { $hours = round($n/(60*60)); return 'about ' . $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago'; }
    if($n < (time() - strtotime('yesterday'))) return 'yesterday';
    if($n < (60*60*24)) { $hours = round($n/(60*60)); return 'about ' . $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago'; }
    if($n < (60*60*24*6.5)) return 'about ' . round($n/(60*60*24)) . ' days ago';
    if($n < (time() - strtotime('last week'))) return 'last week';
    if(round($n/(60*60*24*7))  == 1) return 'about a week ago';
    if($n < (60*60*24*7*3.5)) return 'about ' . round($n/(60*60*24*7)) . ' weeks ago';
    if($n < (time() - strtotime('last month'))) return 'last month';
    if(round($n/(60*60*24*7*4))  == 1) return 'about a month ago';
    if($n < (60*60*24*7*4*11.5)) return 'about ' . round($n/(60*60*24*7*4)) . ' months ago';
    if($n < (time() - strtotime('last year'))) return 'last year';
    if(round($n/(60*60*24*7*52)) == 1) return 'about a year ago';
    if($n >= (60*60*24*7*4*12)) return 'about ' . round($n/(60*60*24*7*52)) . ' years ago'; 
    return false;
  }
  // taken directly from aspire plugin
  function sortByName($a,$b)
  {
      return strcmp($a["name"], $b["name"]);
  }
}

