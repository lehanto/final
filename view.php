<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8"/>
		<meta name="robots" content="noindex,nofollow">
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <style type="text/css">
    html { height: 100% }
    body { height: 100%; margin: 30px; padding: 0 }
    #map { width:50%; height:70%; }
    </style>
		<title>画像詳細</title>
	</head>
<body onload="initialize()">


<?php
header("Content-Type: text/html; charset=UTF-8");

//ライブラリを読み込む
require_once "./get-exif-10from60.php";

//指定された画像をブラウザに出力
//ディレクトリの名前
$dir_name = "./upload";
//ファイル名
$file_name = basename($_GET["f"]);
//ディレクトリのパス
$path = $dir_name . "/" . $file_name;
echo "$path";

echo "<img src=\" $path  \"width=\"500\">";

//マップを表示
echo '<div id="map"></div>';


$img = $path;

//Exifを取得し、値を代入
$exif = @exif_read_data($img);


if(!$exif ||
  !isset($exif["GPSLatitudeRef"]) || empty($exif["GPSLatitudeRef"]) ||
  !isset($exif["GPSLatitude"]) || empty($exif["GPSLatitude"]) ||
  !isset($exif["GPSLongitudeRef"]) || empty($exif["GPSLongitudeRef"]) ||
  !isset($exif["GPSLongitude"]) || empty($exif["GPSLongitude"])
){
  echo '<p style="color:red">画像に位置情報、またはExif自体が含まれていませんでした…。</p>';

  //Exifが取得できた場合
  }else{

    //緯度を60進数から10進数に変換する
    $lat = get_10_from_60_exif($exif["GPSLatitudeRef"],$exif["GPSLatitude"]);

    //経度を60進数から10進数に変換する
    $lng = get_10_from_60_exif($exif["GPSLongitudeRef"],$exif["GPSLongitude"]);

    echo "<h2>Exifデータ</h2>";
    echo '<p><textarea style="width:90%;height:200px;">'.print_r($exif,true).'</textarea></p>';

  }

?>

<!-- Google Maps APIの読み込み -->
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script type="text/javascript">
function initialize() {
  /* 緯度・経度：日本, 表参道駅（東京）*/
  var latlng=new google.maps.LatLng(<?php echo "{$lat},{$lng}";?>);
  /* 地図のオプション設定 */
  var myOptions = {
    /*初期のズーム レベル */
    zoom: 15,
    /* 地図の中心点 */
    center: latlng,
    /* 地図タイプ */
    mapTypeId: google.maps.MapTypeId.ROADMAP
                  };
  var map=new google.maps.Map(document.getElementById("map"), myOptions);
  var marker = new google.maps.Marker({
        position: new google.maps.LatLng(<?php echo "{$lat},{$lng}";?>), //ピンの緯度経度を入力
        map: map,
        title: "hoge" //ピンにマウスカーソルを乗せたときに表示されるタイトルを入力
    });
                }
</script>

</body>
</html>
