<?php
/*
Plugin Name: Animal rights
Plugin URI: http://wordpress.org/extend/plugins/animal-rights-news/
Description: Adds a customizeable widget which displays the latest information and news by http://www.tierrecht.org/
Version: 1.0
Author: Jens Schulze
Author URI: http://www.tierrecht.org/
License: GPL3
*/

function tierrechtnews()
{
  $options = get_option("widget_tierrechtnews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Tierrecht',
      'news' => '5',
      'chars' => '30'
    );
  }

  // RSS Objekt erzeugen 
  $rss = simplexml_load_file( 
  'http://www.tierrecht.org/feed/'); 
  ?> 
  
  <ul> 
  
  <?php 
  // maximale Anzahl an News, wobei 0 (Null) alle anzeigt
  $max_news = $options['news'];
  // maximale Länge, auf die ein Titel, falls notwendig, gekürzt wird
  $max_length = $options['chars'];
  
  // RSS Elemente durchlaufen 
  $cnt = 0;
  foreach($rss->channel->item as $i) { 
    if($max_news > 0 AND $cnt >= $max_news){
        break;
    }
    ?> 
    
    <li>
    <?php
    // Titel in Zwischenvariable speichern
    $title = $i->title;
    // Länge des Titels ermitteln
    $length = strlen($title);
    // wenn der Titel länger als die vorher definierte Maximallänge ist,
    // wird er gekürzt und mit "..." bereichert, sonst wird er normal ausgegeben
    if($length > $max_length){
      $title = substr($title, 0, $max_length)."...";
    }
    ?>
    <a href="<?=$i->link?>"><?=$title?></a> 
    </li> 
    
    <?php 
    $cnt++;
  } 
  ?> 
  
  </ul>
<?php  
}

function widget_tierrechtnews($args)
{
  extract($args);
  
  $options = get_option("widget_tierrechtnews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Tierrecht',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  echo $before_widget;
  echo $before_title;
  echo $options['title'];
  echo $after_title;
  tierrechtnews();
  echo $after_widget;
}

function tierrechtnews_control()
{
  $options = get_option("widget_tierrechtnews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Tierrecht',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  if($_POST['tierrechtnews-Submit'])
  {
    $options['title'] = htmlspecialchars($_POST['tierrechtnews-WidgetTitle']);
    $options['news'] = htmlspecialchars($_POST['tierrechtnews-NewsCount']);
    $options['chars'] = htmlspecialchars($_POST['tierrechtnews-CharCount']);
    update_option("widget_tierrechtnews", $options);
  }
?> 
  <p>
    <label for="tierrechtnews-WidgetTitle">Widget Title: </label>
    <input type="text" id="tierrechtnews-WidgetTitle" name="tierrechtnews-WidgetTitle" value="<?php echo $options['title'];?>" />
    <br /><br />
    <label for="tierrechtnews-NewsCount">Max. News: </label>
    <input type="text" id="tierrechtnews-NewsCount" name="tierrechtnews-NewsCount" value="<?php echo $options['news'];?>" />
    <br /><br />
    <label for="tierrechtnews-CharCount">Max. Characters: </label>
    <input type="text" id="tierrechtnews-CharCount" name="tierrechtnews-CharCount" value="<?php echo $options['chars'];?>" />
    <br /><br />
    <input type="hidden" id="tierrechtnews-Submit"  name="tierrechtnews-Submit" value="1" />
  </p>
  
<?php
}

function tierrechtnews_init()
{
  register_sidebar_widget(__('Tierrecht.org'), 'widget_tierrechtnews');    
  register_widget_control('Tierrecht.org', 'tierrechtnews_control', 300, 200);
}
add_action("plugins_loaded", "tierrechtnews_init");
?>