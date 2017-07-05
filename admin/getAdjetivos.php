<?
include('./App/Lib/class-ws-v1.php');
header('Content-Type: text/html; charset=utf-8');


$letras = array("a"=>35, "b"=>19, "c"=>66, "d"=>23, "e"=>26, "f"=>16, "g"=>13, "h"=>6, "i"=>12, "j"=>5, "k"=>1, "l"=>14, "m"=>28, "n"=>7, "o"=>7, "p"=>14, "q"=>4, "r"=>16, "s"=>12, "t"=>19, "u"=>2, "v"=>9, "x"=>1, "z"=>2, "w"=>1, "y"=>1);
foreach ($letras as $key => $max) {

    for ($i = 1; $i<=$max; $i++) {

       $link =file_get_contents('http://dicionario.aizeta.com/verbetes/substantivo/'.$key.'/'.$i);

      $dom = new DOMDocument();
      @$dom->loadHTML($link);
      $xpath = new DOMXPath($dom);
      $elements=$xpath->query('//div[@class="info-feat"]');

      foreach($elements as $element){
        print_r($element);
        // echo utf8_decode($element->nodeValue).PHP_EOL;

          exit;
      }












    }
}

