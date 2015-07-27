<?php
   $feed = new DOMDocument(); 
   $feed->load($_GET['feed_url']);
   $json = array();
   $files = array();
   $items = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('item');

   foreach($items as $c => $item) {
      $title = $item->getElementsByTagName('title')->item(0)->firstChild->nodeValue;
      $description = $item->getElementsByTagName('description')->item(0)->firstChild->nodeValue;
      $content = $item->getElementsByTagName('encoded')->item(0)->firstChild->nodeValue;
      $creator = $item->getElementsByTagName('creator')->item(0)->firstChild->nodeValue;
      $author = $item->getElementsByTagName('author')->item(0)->firstChild->nodeValue;
      $category = $item->getElementsByTagName('category')->item(0)->firstChild->nodeValue;
      $pubDate = $item->getElementsByTagName('pubDate')->item(0)->firstChild->nodeValue;
      $guid = $item->getElementsByTagName('guid')->item(0)->firstChild->nodeValue;
      $comments = $item->getElementsByTagName('comments')->item(0)->firstChild->nodeValue;
      if ($item->getElementsByTagName('enclosure')->item(0)) {
         $img_url = $item->getElementsByTagName('enclosure')->item(0)->getAttribute('url');
         $files[] = $img_url;
      }
      $json[$c]['title'] = $title;
      $json[$c]['description'] = strip_tags($description);
      $json[$c]['date'] = substr($pubDate, 5, -15);
      $json[$c]['guid'] = $guid;    
      $json[$c]['contents'] = strip_tags($content);
      if ($author) {
         $json[$c]['author'] = $author;
      } else {
         $json[$c]['author'] = $creator;
      }
      $json[$c]['comments'] = $comments;
      $json[$c]['category'] = $category;
      $json[$c]['img_url'] = $img_url;
      $json[$c]['img'] = basename( $img_url);
   }

   $zip = new ZipArchive();
   $tmp_file = tempnam('.','');
   $zip->open($tmp_file, ZipArchive::CREATE);

   foreach($files as $file){
   $download_file = file_get_contents($file);
   $zip->addFromString(basename($file),$download_file);
   }
      
   $zip->addFromString('feed.json',utf8_encode(json_encode($json)));
   $zip->close();

   header('Content-disposition: attachment; filename=download.zip');
   header('Content-type: application/zip');
   readfile($tmp_file);
?>