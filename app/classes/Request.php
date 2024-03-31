<?php
class Request {
    static function makeCurlRequest ($path, $get = false, $post = false, $isJSON = true, $httpHeader = false)
    {
      $ch = curl_init();
      if(!$ch)
        throw new Exception('Ошибка при инициализации запроса');
  
      $request_string = $path;
      if($get)
        $request_string .= '?'.http_build_query($get);
  
      curl_setopt($ch, CURLOPT_URL, $request_string);
  
      if($post) {
        curl_setopt($ch, CURLOPT_POST, true);
        if (is_array($post))
          curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        else
          curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
      }
  
      if ($httpHeader || is_array($httpHeader)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
      } else {
        curl_setopt($ch, CURLOPT_FAILONERROR, true); 
      }
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, 0);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
      curl_setopt($ch, CURLOPT_USERAGENT, 'PHP Bot');
      $request = curl_exec($ch);
  
      if($request === false) {
        (new Logger( FROOT . '/storage/logs/test.log'))->log('CURLError: ' . ' ErroMessage: ' . curl_error($ch) );
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        throw new Exception('Ошибка при запросе '.curl_error($ch), $code);
      }
  
      curl_close($ch);
  
      return $isJSON ? json_decode($request, true) : $request;
    }
  }