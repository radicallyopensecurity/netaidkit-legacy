<?php

class I18n {
    const DOMAIN = 'nakweb';
    const LOCALEDIR = '/nak/webapp/locale';

    static protected $locale = 'en';

    static public function setlocale($locale) {
        putenv("LANG=$locale");
        setlocale(LC_MESSAGES, $locale);
        bindtextdomain(self::DOMAIN, self::LOCALEDIR);
        textdomain(self::DOMAIN);
    }

    static public function setlocale_preference($http_accept_language) {
       foreach(explode(',', $http_accept_language) as $lang) {
           // just parsing this one isn't worth adding pecl as a dependency
           // format: http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
           $twoletter = trim(explode(';', trim($lang))[0]);
           if (@file_exists(self::LOCALEDIR . "/$twoletter")) {
               self::$locale = $twoletter;
               break;
           }
       }
       self::setlocale(self::$locale);
    }
}

?>
