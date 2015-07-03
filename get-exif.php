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
		<title>Exifの位置情報を取得するデモ</title>
	</head>
<body onload="initialize()">

  <!-- 地図の埋め込み表示 -->

<h1>Exifの位置情報を取得するデモ</h1>
<p>PHPの<code>exif_read_data()</code>関数を利用して、指定した写真から位置情報を取得するサンプルデモです。</p>
<?php

	//ライブラリを読み込む
	//変換
	require_once "./get-exif-10from60.php";
	//表生成
	require_once "./print-table.php";
  // アップロードが正常に行われたかチェック
  if ( $_FILES['filename']['error'] == UPLOAD_ERR_OK )
  {
      // アップロード先とファイル名を付与
      $upload_file = "./upload/" . $_FILES["filename"]["name"] ;

      // アップロードしたファイルを指定のパスへ移動
      if ( move_uploaded_file( $_FILES["filename"]['tmp_name'], $upload_file ) )
      {
          // パーミッションを変更
          // Read and write for owner, read for everybody
          chmod($upload_file, 0644);
      }
  }

	//画像ファイルのパス
	$img = "./upload/photo.jpg";

	//Exifを取得し、[$exif]に代入する
  $exif = @exif_read_data($img);

	//Exifが取得できなかった場合
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

  		//結果を出力する
  		echo '<h2>画像ファイル</h2>';
  		echo '<p><img src="'.$img.'" alt="画像ファイル" style="max-width:200px;height:auto;"/></p>';

  		echo '<h2>位置情報</h2>';
      echo "<p>";
  		echo "</p>";
  		echo "{$lat},{$lng}";
        echo '<div id="map"></div>';
      echo "<p>";
  		echo "</p>";

  		echo '<h2>Exifデータ</h2>';
  		echo '<p><textarea style="width:90%;height:200px;">'.print_r($exif,true).'</textarea></p>';
			printExif($img);


  	}

    //ディレクトリ名
    $dir_name = dir("./upload");

    echo "ディレクトリ:" . $dir_name->path . "</p>";
    echo "<p>画像一覧</p>";


    //ディレクトリ内の画像を表示
    while($file_name = $dir_name->read())
    {
      $path = $dir_name->path . "/" . $file_name;
      if (@getimagesize($path))
      {
        echo "<a href=\"view.php?f=$path\"\" $path  \"target=\"_blank\">";
        echo "<img src=\" $path  \"width=\"100\"></a> ";
      }
    }
    $dir_name->close();

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
        })



                    }
    </script>

    <form action="get-exif.php" method="post" enctype="multipart/form-data">
        <input type="file" name="filename">
        <input type="submit" name="submit" value="送信" />
    </form>

  </body>
  </html>
