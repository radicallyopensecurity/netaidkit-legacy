<?php

class I18n {
    const DEFAULT_LOCALE = 'en';
    const DOMAIN = 'nakweb';
    const LOCALEDIR = '/nak/webapp/locale';
    const LANGFILE = ROOT_DIR . '/data/lang';

    static public function setlocale() {
        self::setlocale_twoletter(self::getlocale());
    }

    static public function getlocale() {
        if (self::settings_get_language() !== false)
            return self::settings_get_language();
        return self::getlocale_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);
    }

    static public function setlocale_twoletter($locale) {
        putenv("LANG=$locale");
        setlocale(LC_MESSAGES, $locale);
        bindtextdomain(self::DOMAIN, self::LOCALEDIR);
        textdomain(self::DOMAIN);
    }

    static public function getlocale_http($http_accept_language) {
       foreach(explode(',', $http_accept_language) as $lang) {
           // just parsing this one isn't worth adding pecl as a dependency
           // format: http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
           $twoletter = trim(explode(';', trim($lang))[0]);
           if (@file_exists(self::LOCALEDIR . "/$twoletter")) {
               return $twoletter;
           }
       }
       return self::DEFAULT_LOCALE;
    }

    static public function iso639_1() {
        return json_decode(file_get_contents(ROOT_DIR . '/iso639-1.json'), true);
    }

    static public function available_languages() {
        $localedirs = scandir(self::LOCALEDIR);

        // remove .. + .
        unset($localedirs[0]); // index remains unchanged
        unset($localedirs[1]);

	return $localedirs;
    }

    static public function available_languages_iso639_1() {
        $localizations = self::available_languages();
        $isotable = self::iso639_1();
        $ret = array();

        foreach ($localizations as $localization) {
            $ret[$localization] = $isotable[$localization];
        }
        return $ret;
    }

    static public function available_languages_display_fmt() {
        $display_lang = self::getlocale();
        $available = self::available_languages_iso639_1();
        $ret = array();

        foreach($available as $code => $lang) {
	    $lang_native = $display_lang == $code ? ''
		: ' (' . $lang[$code] . '/' . $lang[self::DEFAULT_LOCALE] . ')';
            $ret[$code] = $lang[$display_lang] . $lang_native;
	}
        return $ret;
    }

    static public function settings_set_language($twoletter) {
       if (!in_array(trim($twoletter), self::available_languages()))
           die('Language not supported.');

       file_put_contents(self::LANGFILE, $twoletter);
    }

    static public function settings_get_language() {
        if (!file_exists(self::LANGFILE))
            return false;
        return trim(file_get_contents(self::LANGFILE));
    }

    static public function settings_get_autodetect() {
        return !file_exists(self::LANGFILE);
    }

    static public function settings_set_autodetect() {
        @unlink(self::LANGFILE);
    }
}

?>
