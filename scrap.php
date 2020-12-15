<?php
include 'simple_html_dom.php';
$url = "https://thebjav.com/category/korean-bj/";

$html = file_get_html($url);
$lastPage = $html->find('div[class=pagination]',0)->find('ul',0)->find('li',-1)->find('a',0)->href;
for ($i=1; $i < basename($lastPage); $i++) { 
    echo 'Pagination '.$i. ' in '.$url.PHP_EOL;
    $html = file_get_html($url.'page/'.$i);
    getData($html);
}
function getVideo($url,$title)
{
    $html = file_get_html($url);
    $id = basename( $html->find('meta[itemprop=embedURL]',0)->content);
    $ticket = getTicket($id);
    sleep(3);
    $download = getDownload($id,$ticket)->url;
    $video_name = getDownload($id,$ticket)->name;
    $uploud_url = UploudUrl($download,$video_name);
    $data = [
        'name' => $title,
        'video' => [
            'url_uploud' => $uploud_url,
            'original_download' => $download,
            'video_name' => $video_name
        ]
    ];
    echo json_encode($data,JSON_PRETTY_PRINT);
    return json_encode($data,JSON_PRETTY_PRINT);
    
}
function getData($html)
{
foreach ($html->find('article') as $key => $value) {
    $title = $value->find('a',0)->title;
    $url = $value->find('a',0)->href;
       $data =  getVideo($url,$title);
       file_put_contents('results.json',$data,FILE_APPEND);
    }
}
function getTicket($id)
{
    $url = 'https://api.streamtape.com/file/dlticket?file='.$id.'&login=6689456893cfc0e063e9&key=aRMr8a9lmVHxqD6';
     $ch = curl_init(); 
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
     $output = curl_exec($ch); 
     curl_close($ch);      
     $result = json_decode($output)->result->ticket;
     return $result;
}
function getDownload($file,$ticket)
{
    $url = 'https://api.streamtape.com/file/dl?file='.$file.'&ticket='.$ticket;
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $output = curl_exec($ch); 
    curl_close($ch);      
    $result = json_decode($output)->result;
    return $result;
}
function UploudUrl($data,$video_name)
{
   $url = 'https://api.streamtape.com/remotedl/add?login=6689456893cfc0e063e9&key=aRMr8a9lmVHxqD6&url='.$data.'&folder=DX21KY9RT8M&name='.$video_name;
   $ch = curl_init(); 
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
   $output = curl_exec($ch); 
   curl_close($ch);      
   $result = json_decode($output)->result->link;
   return $result;
}