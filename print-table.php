<?php
  /**
   * 指定された画像の EXIF 情報をテーブルとして出力します。
   *
   * @param   $path   画像のパス。
   */
  function printExif( $path )
  {
      $exif = exif_read_data( $path );
      if( !$exif ) { return; }

      $text = "";
      foreach( $exif as $key => $value )
      {
          $text .= "<tr><td>" . $key . "</td><td>" . $value . "</td></tr>";
      }

      echo( "<table border='1' width='500' cellspacing='0' cellpadding='5' bordercolor='#333333'>" . $text . "</table>" );
  }
?>
