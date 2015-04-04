<?php

use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

function shorten($length = 5){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function replace($text) {
 $breaks = array("<br />");
 $txt = str_ireplace($breaks, "\r\n", $text);
 return $txt;
}

function nl2br2($string) {
 $str = str_replace(array("\r\n", "\r", "\n"), "<br />", $string);
 $str = trim($str);
 return $str;
}

/**
 * Send emails
 *
 * @param $htmlBody 
 * @param $textBody 
 * @param $subject 
 * @param $to email 
 * @param $toName
 * 
 * @return no return
 */
function sendMail($htmlBody, $textBody, $subject, $to, $toName) {
 $htmlPart = new MimePart($htmlBody);
 $htmlPart->type = "text/html";

 $textPart = new MimePart($textBody);
 $textPart->type = "text/plain";

 $body = new MimeMessage();
 $body->setParts(array($textPart, $htmlPart));

 $message = new Mail\Message();
 $message->setFrom("no-reply@hashcreate.com", "Ibn Batutta");
 $message->addTo($to, $toName);
 $message->setSubject($subject);

 $message->setEncoding("UTF-8");
 $message->setBody($body);
 $message->getHeaders()->get('content-type')->setType('multipart/alternative');

 $transport = new Mail\Transport\Sendmail();
 $transport->send($message);
}

/**
 * Generate a time in readable way
 *
 * @param $time date from
 * @return time
 */
function ago($time) {
 $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
 $lengths = array("60", "60", "24", "7", "4.35", "12", "10");

 $now = time();

 $difference = $now - $time;
 $tense = "ago";

 for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
  $difference /= $lengths[$j];
 }

 $difference = round($difference);

 if ($difference != 1) {
  $periods[$j].= "s";
 }

 return "$difference $periods[$j] ago";
}

/**
 * Generate a time in readable way
 *
 * @param $dateFrom date from
 * @param $dateTo date to
 * @return time
 */
function timeSince($dateFrom, $dateTo) {
// array of time period chunks
 $chunks = array(
     array(60 * 60 * 24 * 365, 'year'),
     array(60 * 60 * 24 * 30, 'month'),
     array(60 * 60 * 24 * 7, 'week'),
     array(60 * 60 * 24, 'day'),
     array(60 * 60, 'hour'),
     array(60, 'minute'),
 );

 $original = strtotime($dateFrom);
 $now = strtotime($dateTo);
 $since = $now - $original;
 $message = ($now < $original) ? '-' : null;

// If the difference is less than 60, we will show the seconds difference as well
 if ($since < 60) {
  $chunks[] = array(1, 'second');
 }

// $j saves performing the count function each time around the loop
 for ($i = 0, $j = count($chunks); $i < $j; $i++) {

  $seconds = $chunks[$i][0];
  $name = $chunks[$i][1];

// finding the biggest chunk (if the chunk fits, break)
  if (($count = floor($since / $seconds)) != 0) {
   break;
  }
 }

 $print = ($count == 1) ? '1 ' . $name : $count . ' ' . $name . 's';

 if ($i + 1 < $j) {
// now getting the second item
  $seconds2 = $chunks[$i + 1][0];
  $name2 = $chunks[$i + 1][1];

// add second item if it's greater than 0
  if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
   $print .= ($count2 == 1) ? ', 1 ' . $name2 : ', ' . $count2 . ' ' . $name2 . 's';
  }
 }
 return $message . $print;
}

/**
 * Clean the text from spaces and special characters
 *
 * @param $text  to clean
 * @return clean text
 */
function cleanText($text) {
 return preg_replace('/[^A-Za-z0-9]/', "", $text);
}

/**
 * vd
 * Short cut to var_dump
 * 
 * @param $array
 * 
 * @return var_dump
 */
function vd($array, $die = 0) {
 echo "<pre>";
 if ($die == 0) {
  die(var_dump($array));
 } else {
  echo(var_dump($array));
 }
}

/**
 * uniqueArray
 * Unique array (Without duplicates elements)
 * 
 * @param $array
 * 
 * @return array
 */
function uniqueArray($array) {
 return array_map("unserialize", array_unique(array_map("serialize", $array)));
}

/**
 * isImage
 * Checks if the files is image and it has width and height
 * 
 * @param $tempFile
 * 
 * @return boolean
 */
function isImage($tempFile) {
 $size = getimagesize($tempFile);
 if (isset($size) && $size[0] && $size[1] && $size[0] * $size[1] > 0) {
  return true;
 } else {
  return false;
 }
}

/**
 * ResizeImage
 * http://www.white-hat-web-design.co.uk/blog/resizing-images-with-php/
 * 
 * 
 */
class ResizeImage {

 var $image;
 var $image_type;

 function load($filename) {
  $image_info = getimagesize($filename);
  $this->image_type = $image_info[2];
  if ($this->image_type == IMAGETYPE_JPEG) {
   $this->image = imagecreatefromjpeg($filename);
   $img_type = "image/jpeg";
  } elseif ($this->image_type == IMAGETYPE_GIF) {
   $this->image = imagecreatefromgif($filename);
   $img_type = "image/gif";
  } elseif ($this->image_type == IMAGETYPE_PNG) {
   $this->image = imagecreatefrompng($filename);
   $img_type = "image/png";
  }
  return $img_type;
 }

 function save($filename, $image_type = IMAGETYPE_JPEG, $compression = 75, $permissions = null) {
  // do this or they'll all go to jpeg
  $image_type = $this->image_type;

  if ($image_type == IMAGETYPE_JPEG) {
   imagejpeg($this->image, $filename, $compression);
  } elseif ($image_type == IMAGETYPE_GIF) {
   imagegif($this->image, $filename);
  } elseif ($image_type == IMAGETYPE_PNG) {
   // need this for transparent png to work          
   imagealphablending($this->image, false);
   imagesavealpha($this->image, true);
   imagepng($this->image, $filename);
  }
  if ($permissions != null) {
   chmod($filename, $permissions);
  }
 }

 function output($image_type = IMAGETYPE_JPEG) {
  if ($image_type == IMAGETYPE_JPEG) {
   imagejpeg($this->image);
  } elseif ($image_type == IMAGETYPE_GIF) {
   imagegif($this->image);
  } elseif ($image_type == IMAGETYPE_PNG) {
   imageAlphaBlending($this->image, true);
   imageSaveAlpha($this->image, true);
   imagepng($this->image);
  }
 }

 function getWidth() {
  return imagesx($this->image);
 }

 function getHeight() {
  return imagesy($this->image);
 }

 function resizeToHeight($height) {
  $ratio = $height / $this->getHeight();
  $width = $this->getWidth() * $ratio;
  $this->resize($width, $height);
 }

 function resizeToWidth($width) {
  $ratio = $width / $this->getWidth();
  $height = $this->getheight() * $ratio;
  $this->resize($width, $height);
 }

 function scale($scale) {
  $width = $this->getWidth() * $scale / 100;
  $height = $this->getheight() * $scale / 100;
  $this->resize($width, $height);
 }

 function resize($width, $height, $forcesize = 'n') {

  //imageinterlace($this->image, true);

  /* optional. if file is smaller, do not resize. */
  if ($forcesize == 'n') {
   if ($width > $this->getWidth() && $height > $this->getHeight()) {
    $width = $this->getWidth();
    $height = $this->getHeight();
   }
  }

  $new_image = imagecreatetruecolor($width, $height);

  /* Check if this image is PNG or GIF, then set if Transparent */
  if (($this->image_type == IMAGETYPE_GIF) || ($this->image_type == IMAGETYPE_PNG)) {
   imagealphablending($new_image, false);
   imagesavealpha($new_image, true);
   $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
   imagefilledrectangle($new_image, 0, 0, $width, $height, $transparent);
  }
  imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());

  $this->image = $new_image;
 }

}

/**
 * toNumber
 * @param $number to trim
 * 
 * @return int
 */
function toNumber($number) {
 return preg_replace('/[^0-9]/', '', $number);
}

/**
 * generateDay
 * Generate day
 * $i++
 * @param @start $i = $start
 * @param @end $i <= $end
 * @param $selected = 0
 * 
 * @return option
 */
function generateDay($start = 1, $end = 31, $selected = 0) {
 $option = "";
 for ($i = $start; $i <= $end; $i++) {
  if ($selected > 0 && $selected == $i) {
   $option .= "<option value='" . $i . "' selected>" . $i . "</option>";
  } else {
   $option .= "<option value='" . $i . "'>" . $i . "</option>";
  }
 }
 return $option;
}

/* / 
 * generateMonth
 * Generate month
 * $i++
 * @param @start $i = $start
 * @param @end $i <= $end
 * @param $selected = 0
 * 
 * @return option
 */

function generateMonth($start = 1, $end = 12, $selected = 0) {
 $option = "";
 for ($i = $start; $i <= $end; $i++) {
  if ($selected > 0 && $selected == $i) {
   $option .= "<option value='" . $i . "' selected>" . $i . " - " . getMonth($i) . "</option>";
  } else {
   $option .= "<option value='" . $i . "'>" . $i . " - " . getMonth($i) . "</option>";
  }
 }
 return $option;
}

/**
 * generateYear
 * Generate year
 * $i--
 * @param @start $i = $start
 * @param @end $i > = $end
 * @param $selected = 0
 * 
 * @return option
 */
function generateYear($start = 2013, $end = 1905, $selected = 0) {
 $option = "";
 for ($i = $start; $i >= $end; $i--) {
  if ($selected > 0 && $selected == $i) {
   $option .= "<option value='" . $i . "' selected>" . $i . "</option>";
  } else {
   $option .= "<option value='" . $i . "'>" . $i . "</option>";
  }
 }
 return $option;
}

/**
 * sortDate
 * sortDay to form Month day, year to Month day, year
 * 
 * @param $fromMonth
 * @param $fromDay
 * @param $fromYear
 * 
 * @param $toMonth
 * @param $toDay
 * @param $toYear
 * 
 * 
 * @return string
 * 
 */
function sortDate($fromDay, $fromMonth, $fromYear, $toDay, $toMonth, $toYear) {

 if ($fromMonth >= 1 && $fromDay >= 1) {
  $from_comma1 = " ";
  $from_comma2 = ", ";
 } else if ($fromMonth < 1 && $fromDay < 1) {
  $fromDay = "";
  $from_comma1 = "";
  $from_comma2 = "";
 } else if ($fromMonth < 1 && $fromDay >= 1) {
  $from_comma1 = "";
  $from_comma2 = "";
  $fromDay = "";
 } elseif ($fromDay <= 0) {
  $fromDay = "";
  $from_comma1 = ", ";
 } else {
  $from_comma1 = ", ";
  $from_comma2 = "";
 }

 if ($toMonth >= 1 && $toDay >= 1) {
  $to_comma1 = " ";
  $to_comma2 = ", ";
 } else if ($toMonth < 1 && $toDay < 1) {
  $toDay = "";
  $to_comma1 = "";
  $to_comma2 = "";
 } else if ($toMonth < 1 && $toDay >= 1) {
  $to_comma1 = "";
  $to_comma2 = "";
  $toDay = "";
 } elseif ($toDay <= 0) {
  $toDay = "";
  $to_comma1 = ", ";
 } else {
  $to_comma1 = ", ";
  $to_comma2 = "";
 }

 if ($toYear == 0 && $toMonth == 0 && $toDay == 0) {
  $to = "<b>present</b>";
 } else {
  $to = getMonth($toMonth) . $to_comma1 . $toDay . $to_comma2 . $toYear;
 }

 return getMonth($fromMonth) . $from_comma1 . $fromDay . $from_comma2 . $fromYear . " to " . $to;
}

/**
 * uploadImage
 * 
 * @param $tempFile
 * @param $targetFile
 * @param $uploadDir
 * @param $option 
 * @param $option[0] resize/scale
 * @param $option[1] width
 * @param $option[2] height
 * @param $option[3] s/n
 * 
 * @return boolean
 */
function uploadImage($tempFile, $targetFile, $uploadDir, $option, $name_s) {
 if (move_uploaded_file($tempFile, $targetFile)) {
  $rezImage = new ResizeImage();
  $rezImage->load($targetFile);
  if ($option[0] == "resize") {
   $rezImage->resize($option[1], $option[2], $option[3]);
  } else if ($option[0] == "scale") {
   $rezImage->scale($option[1]);
  }
  $rezImage->save($uploadDir . $name_s);
  return true;
 } else {
  return false;
 }
}

/**
 * vali Validate
 * 
 * @param $var
 * @param $message
 * 
 * @return json
 */
function vali($var, $message) {

 if (!isset($var) || $var == "0" || empty($var)) {
  $res['status'] = '0';
  $res['message'] = $message;

  die(json_encode($res));
 }
 return true;
}

function message($status, $message, $arr = array()) {
 $res = array();
 $res['status'] = $status;
 $res['message'] = $message;
 $f_res = array_merge($res, $arr);
 die(json_encode($f_res));
}

?>
