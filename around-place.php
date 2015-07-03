<!DOCTYPE html>
<html lang="ja">
<head>
   <meta charset="utf-8" />
   <title>設置サンプル - GMAPv3 - プレイスライブラリ - 全ての場所タイプ</title>
   <link rel="stylesheet" type="text/css" href="css/gmapv3.css" />
   <style type="text/css">
   html { height: 100% }
   body { height: 100%; margin: 30px; padding: 0 }
   #map { width:50%; height:70%; }
   </style>
   <!-- スマートフォン向けviewportの指定 -->
   <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
   <!-- jQuery -->
   <script src="http://maps.google.com/maps/api/js?sensor=true&libraries=places"></script>
   <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
   <script type="text/javascript">
      var map, service, point,openFLG=[],overlays=[],iterator=0,current;
      var lat=35.664122;
      var lng=139.729426;
      var openFLG=[],iterator=0;
      var picmaxWidth=200; /* スポット画像の最大幅 */
      var picmaxHeight=200; /* スポット画像の最大高さ */

      /* ページ読み込み時に地図を初期化 */
      $(function(){
         initialize("store");
         $("#places").bind("change",function(){
            fGetPlaceInfo();
         });
         $("#btn").click(function(e){
            /* 現在位置情報を取得 */
            navigator.geolocation.watchPosition(function(position) {
               point=new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
               fGetPlaceInfo();
            },null,{enableHighAccuracy:false});
         });
         /* オーバーレイ全削除 */
         function resetOverlay(deleteFLG){
            if(overlays.length>0){
               for(i in overlays){
                  overlays[i][1].close();
                  if(deleteFLG==1){
                     openFLG[i]=0;
                     overlays[i][0].setMap(null);
                  }
               }
               if(deleteFLG==1) overlays.length=0;
               if(deleteFLG==1) iterator=0;
            }
         }
         /* 地図初期化 */
         function initialize() {
            point=new google.maps.LatLng(lat,lng);
            map=new google.maps.Map(document.getElementById('map'), {
               center: point,
               zoom:16,
               mapTypeId: google.maps.MapTypeId.ROADMAP,
               scrollwheel: false /* スクロールホイールによる拡大・縮小無効化 */
            });
            service=new google.maps.places.PlacesService(map);
            fGetPlaceInfo();
         }
         function fGetPlaceInfo(){
            map.setCenter(point);
            resetOverlay(1);
            var request={
               location: point,
               radius:"500" /* 指定した座標から半径500m以内 */
            };
            service.search(request, callback);
         }
         function callback(results, status) {
            dbg(results.length);
            if (status==google.maps.places.PlacesServiceStatus.OK && results.length>0){
               for (var i=0; i<results.length; i++) {
                  var place=results[i];
                  createMarker(results[i]);
                  iterator++;
               }
            }else{
               alert("スポット情報が見つかりませんでした。");
            }
         }
         function createMarker(place) {
            var placeLoc=place.geometry.location;
            /* マーカー */
            var marker=new google.maps.Marker({
               map: map,
               position: new google.maps.LatLng(placeLoc.lat(), placeLoc.lng())
            });
            /* 情報ウィンドウ */
            var infowindow=new google.maps.InfoWindow({
               maxWidth:320
            });
            /* ID、フラグセット */
            marker.set("id",iterator);
            infowindow.set("id",iterator);
            overlays.push([marker,infowindow]);
            /* 情報ウィンドウの×ボタンと押した時 */
            google.maps.event.addListener(infowindow,"closeclick",function(){
               openFLG[infowindow.get("id")]=0;
            });
            /* マーカークリックで情報ウィンドウを開閉 */
            google.maps.event.addListener(marker, "click", function(){
               var id=this.get("id");
               if(current>=0 && current!=id){
                  openFLG[current]=0;
               }
               resetOverlay(0);
               var s ="";
               /* アイコン+場所名 */
               s+="<div class='ttl cf'>";
               s+=(place.icon)?"<img width='32' height='32' src='"+place.icon+"' style='float:left;margin-right:5px;' />":"";
               s+=(place.name)?"<b>"+place.name+"</b>":"不明";
               s+="</div>";
               s+="<div class='detail'>";
               /* 住所 */
               if(place.vicinity){
                  s+="<p>"+place.vicinity+"</p>";
               }
               /* 場所タイプ */
               if(place.types){
                  s+="<p>";
                  $.each(place.types,function(x,type){
                     s+=(places_types[type])?places_types[type]+"　":"";
                  });
                  s+="</p>";
               }
               /* 今営業中か */
               if(place.opening_hours){
                  s+="<p>只今営業中！</p>";
               }
               /* 評価 */
               if(place.rating){
                  s+="<p>評価："+place.rating+"</p>";
               }
               /* 写真 */
               if(place.photos && place.photos.length>=1){
                  s+="<p class='picframe'><img src='"+place.photos[0].getUrl({"maxWidth":picmaxWidth,"maxHeight":picmaxHeight})+"' class='shadow size' /></p>";
               }
               current=id;
               var infowindow=overlays[id][1];
               infowindow.setContent("<div class='infowin'>"+s+"</div>");
               if(openFLG[id]!=1){
                  infowindow.open(map, this);
                  openFLG[id]=1;
               }else{
                  infowindow.close();
                  openFLG[id]=0;
               }
            });
         }
         /* プレイスタイプ */
        var places_types={
           "accounting":"会計事務所",
           "airport":"空港",
           "amusement_park":"遊園地",
           "aquarium":"水族館",
           "art_gallery":"アート ギャラリー",
           "atm":"ATM",
           "bakery":"ベーカリー、パン屋",
           "bank":"銀行",
           "bar":"居酒屋",
           "beauty_salon":"ビューティー サロン",
           "bicycle_store":"自転車店",
           "book_store":"書店",
           "bowling_alley":"ボウリング場",
           "bus_station":"バスターミナル",
           "cafe":"カフェ",
           "campground":"キャンプ場",
           "car_dealer":"カー ディーラー",
           "car_rental":"レンタカー",
           "car_repair":"車の修理",
           "car_wash":"洗車場",
           "casino":"カジノ",
           "cemetery":"墓地",
           "church":"教会",
           "city_hall":"市役所",
           "clothing_store":"衣料品店",
           "convenience_store":"コンビニエンス ストア",
           "courthouse":"裁判所",
           "dentist":"歯科医",
           "department_store":"百貨店",
           "doctor":"医者",
           "electrician":"電気工",
           "electronics_store":"電器店",
           "embassy":"大使館",
           "establishment":"施設",
           "finance":"金融業",
           "fire_station":"消防署",
           "florist":"花屋",
           "food":"食料品店",
           "funeral_home":"葬儀場",
           "furniture_store":"家具店",
           "gas_station":"ガソリンスタンド",
           "general_contractor":"建設会社",
           "geocode":"ジオコード",
           "grocery_or_supermarket":"スーパー",
            "gym":"スポーツクラブ",
            "hair_care":"ヘアケア",
            "hardware_store":"金物店",
            "health":"健康",
            "hindu_temple":"ヒンドゥー寺院",
            "home_goods_store":"インテリア ショップ",
            "hospital":"病院",
            "insurance_agency":"保険代理店",
            "jewelry_store":"宝飾店",
            "laundry":"クリーニング店",
            "lawyer":"弁護士",
            "library":"図書館",
            "liquor_store":"酒店",
            "local_government_office":"役場",
            "locksmith":"錠屋",
            "lodging":"宿泊施設",
            "meal_delivery":"出前",
            "meal_takeaway":"テイクアウト",
            "mosque":"モスク",
            "movie_rental":"DVD レンタル",
            "movie_theater":"映画館",
            "moving_company":"引越会社",
            "museum":"美術館/博物館",
            "night_club":"ナイト クラブ",
            "painter":"塗装業",
            "park":"公園",
            "parking":"駐車場",
            "pet_store":"ペット ショップ",
            "pharmacy":"薬局",
            "physiotherapist":"理学療法士",
            "place_of_worship":"礼拝所",
            "plumber":"配管工",
            "police":"警察",
            "post_office":"郵便局",
            "real_estate_agency":"不動産業",
            "restaurant":"レストラン",
            "roofing_contractor":"防水工事業",
            "rv_park":"オート キャンプ場",
            "school":"学校",
            "shoe_store":"靴屋",
            "shopping_mall":"ショッピング モール",
            "spa":"温泉、スパ",
            "stadium":"スタジアム",
            "storage":"倉庫",
            "store":"小売店",
            "subway_station":"地下鉄駅",
            "synagogue":"シナゴーグ",
            "taxi_stand":"タクシー乗り場",
            "train_station":"駅",
           "travel_agency":"旅行代理店",
           "university":"大学",
           "veterinary_care":"獣医",
           "zoo":"動物園",
           "administrative_area_level_1":"行政区画レベル 1",
           "administrative_area_level_2":"行政区画レベル 2",
           "administrative_area_level_3":"行政区画レベル 3",
           "colloquial_area":"非公式地域",
           "country":"国",
           "floor":"階",
           "intersection":"交差点",
           "locality":"地区",
           "natural_feature":"地勢",
           "neighborhood":"周辺地域",
           "political":"政治",
           "point_of_interest":"スポット",
           "post_box":"ポスト",
           "postal_code":"郵便番号",
           "postal_code_prefix":"郵便番号のプレフィックス",
           "postal_town":"郵便番号に対応する都市",
           "premise":"建物名",
           "room":"部屋",
           "route":"ルート",
           "street_address":"住所",
           "street_number":"番地",
           "sublocality":"下位地区",
           "sublocality_level_4":"下位地区レベル 4",
           "sublocality_level_5":"下位地区レベル 5",
           "sublocality_level_3":"下位地区レベル 3",
           "sublocality_level_2":"下位地区レベル 2",
           "sublocality_level_1":"下位地区レベル 1",
           "subpremise":"区画",
           "transit_station":"駅、停留所"
        };
        function dbg(str){
           try{
              if(window.console && console.log){
                 console.log(str);
              }
           }catch(err){
              //alert("error:"+err);
           }
        }
     });
  </script>
</head>
<body>
   <h1>周辺施設</h1>
   <!-- 地図の埋め込み表示 -->
   <div id="map"></div>
</body>
</html>
