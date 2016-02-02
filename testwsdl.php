<?php
ini_set('max_execution_time', 3000);
//location store video
$store_path = "D:\\Users\\";
$download_host = "http://d2d1lc2xkfwsx2.cloudfront.net";
$CJ_MEDIA_URL = "http://pipapi.itvs.cjenm.com/json/data/v1.0/global/clipmediainfo.asp?type=all&platform=viettel";
$IMG_PATH = "D:\\Users\\image\\";

//PROXY 
$aContext = array(
    'http' => array(
        'proxy' => 'tcp://192.168.193.13:3128',
        'request_fulluri' => true,
    ),
);
$cxContext = stream_context_create($aContext);
//MYSQL CONFIG
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "laravel5";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully <br />";

function downloadFile($url, $path)
{
    $newfname = $path;
    $file = fopen ($url, 'r', False,  $GLOBALS['cxContext']);
    if ($file) {
        $newf = fopen ($newfname, 'wb');
        if ($newf) {
            while(!feof($file)) {
                fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
            }
        }
    }
    if ($file) {
        fclose($file);
    }
    if ($newf) {
        fclose($newf);
    }
}

function downloadUrlToFile($url, $outFileName)
{
    //file_put_contents($xmlFileName, fopen($link, 'r'));
    //copy($link, $xmlFileName); // download xml file

    if(is_file($url)) {
        copy($url, $outFileName); // download xml file
    } else {
        $options = array(
          CURLOPT_FILE    => fopen($outFileName, 'w'),
          CURLOPT_TIMEOUT =>  28800, // set this to 8 hours so we dont timeout on big files
          CURLOPT_URL     => $url
        );

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        curl_exec($ch);
        curl_close($ch);
    }
}

function CreateFolder($programID, $url)
{
	if (!file_exists($GLOBALS['IMG_PATH'].$programID)) {
		mkdir($GLOBALS['IMG_PATH'].$programID, 0755, true);
	}
	if(!empty($url))
		downloadFile($url, $GLOBALS['IMG_PATH'].$programID."\\". basename($url));	
}
//lay du lieu tu url
$sFile = file_get_contents($CJ_MEDIA_URL, False, $cxContext);
//lay ra du lieu dang array tu du lieu lay ve
$datas = json_decode($sFile);
$count = 0;

foreach($datas as $data){
	$sql = "insert into films(title, short_summary, slug, user_id, sub_cat_id, running_time) values('" . 
						$data->title ."', '" . $data->contenttitle . "', '" . $data->programid ."', '1', '3', '" . $data->playtime. "')";
	//"http://image.watchon.cjem.skcdn.com/VOD/GA/B120169270/B120169270_EPI0005_01_B.jpg";
	//tao moi folder image theo Program ID 
	CreateFolder($data->programid, $data->contentimg);
	$data->mediaurl = str_replace("_t35", "_t33", $data->mediaurl);
	//download film
	try{
		//downloadFile($media_url, $store_path. basename($media_url));	
		downloadFile($download_host.$data->mediaurl, $store_path. basename($data->mediaurl));	
		echo "finish downloading " . basename($data->mediaurl);
	}catch(Exception $e){
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	echo $sql;
	$conn->query("SET NAMES 'UTF8'");
	if ($conn->query($sql) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
$count++;

	if($count == 2)
		break;
	echo "<br />";
}$conn->close();
echo count($data). '  te';
//var_dump($data[0]);
//lay ra du lieu roi chen vao database
//lay thong tin metadata
//lay thong tin media content
//download file khi khong dung ham chung
//file_put_contents($store_path."Tmpfile.mp4", fopen("http://d2d1lc2xkfwsx2.cloudfront.net/CLIP/SA/B120158441/B120158441_EPI0001_23_t31.mp4", 'r', False, $cxContext));
$media_url = $download_host."/CLIP/SA/B120158441/B120158441_EPI0001_23_t31.mp4";
try{
	//downloadFile($media_url, $store_path. basename($media_url));	
	downloadFile($media_url, $store_path. basename($media_url));	
	echo "finish downloading " . basename($media_url);
}catch(Exception $e){
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}

//insert into mysql



?> 