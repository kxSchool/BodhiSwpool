<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019-06-06
 * Time: 15:30
 */

class view{
    private static $templatePath;
    private static $data;

    static function setTemplatePath() {
        self::$templatePath = APP_PATH."/views";
    }

    static function assign($key, $value) {
        if(is_array($key)) {
            self::$data = array_merge(self::$data, $key);
        } elseif(is_string($key)) {
            self::$data[$key] = $value;
        }
    }

    static function display($template) {
        view::setTemplatePath();
        extract(self::$data);
        ob_start();
        include (self::$templatePath . $template.".php");
        $res = ob_get_contents();
        ob_end_clean();
        return $res;
    }
}