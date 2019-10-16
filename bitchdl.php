<?php 

$url = $argv[1];

$url = str_replace('embed', 'video', $url);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
$content = curl_exec($ch);
curl_close($ch);

@$dom = DOMDocument::loadHTML($content);
$video_title = $dom->getElementsByTagName('title')[0]->textContent;
$channel_name = getElementByClassName($dom, 'video-card-channel')[0]->textContent;

$urls = string_extract_urls($content);
var_dump($urls);
$video_url = array_values(find_string_in_array($urls, '.mp4'))[0];

$channel_name = escape_filename($channel_name);
$video_title = escape_filename($video_title);

$filename = "$channel_name - $video_title.mp4";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $video_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//curl_setopt($start, CURLOPT_SSLVERSION, 3);
$file_data = curl_exec($ch);
curl_close($ch);
$file = fopen($filename, 'w+');
fputs($file, $file_data);
fclose($file);

function escape_filename($in) {
    return preg_replace('/[^A-Za-z0-9 _\-]/', '_', $in);
}

function string_extract_urls($str) {
    preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $str, $match);
    return $match[0];
}

function getElementByClassName($domdocument, $classname) {
    $finder = new DomXPath($domdocument);
    $classname="video-card-channel";
    return $finder->query("//*[contains(@class, '$classname')]");
}

function find_string_in_array ($arr, $string) {
    return array_filter($arr, function($value) use ($string) {
        return strpos($value, $string) !== false;
    });
}

?>
