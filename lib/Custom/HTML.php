<?php

/**
 * HTML/模板处理函数
 *
 */

class Custom_HTML {  
    /**
     * 根据数组生成下拉框HTML
     *
     * @param string $xArr
     * @param string|array $selected 默认选中的值
     * @param string $vIndex
     * @return html string
     */
    public static function getSelectMenu($xArr, $selected = false, $vIndex = NULL){
        $html = '';
        if($xArr){
            foreach($xArr as $key=>$value){
                $_selected = $selected !== false ? (is_array($selected) ? in_array($key, $selected) : $key == $selected) : 0;
                $_selectedStr = $_selected ? 'selected="selected"' : '';
                if($vIndex !== NULL){
                    $value = $value[$vIndex];
                }
                $html .= "<option value=\"{$key}\" {$_selectedStr}>{$value}</option>";
            }
        }
        return $html;
    }
    
    /**
     * 加载 CSS
     *
     * @param array $cssArr CSS文件名数组（不含后缀）
     * @return html string
     */
    public static function loadCss($cssArr){
        if(!$cssArr){
            return '';
        }
        if(!is_array($cssArr)){
            $cssArr = array($cssArr);
        }
        $cssPath = $GLOBALS['GLOBAL_CONF']['CSS_PATH'];
        $webVersion = $GLOBALS['GLOBAL_CONF']['WEB_VERSION'];
        $echo = '';
        foreach($cssArr as $cssFile){
            $echo .= '<link type="text/css" rel="stylesheet" href="' . $cssPath . '/css/' . $cssFile . '.css?v=' . $webVersion . '" />';
        }
        return $echo;
    }

    /**
     * 加载 js
     *
     * @param array $jsArr CSS文件名数组（不含后缀）
     * @return html string
     */
    public static function loadJs($jsArr){
        if(!$jsArr){
            return '';
        }
        if(!is_array($jsArr)){
            $jsArr = array($jsArr);
        }
        $jsPath = $GLOBALS['GLOBAL_CONF']['JS_PATH'];
        $webVersion = $GLOBALS['GLOBAL_CONF']['WEB_VERSION'];
        $echo = '';
        foreach($jsArr as $jsFile){
            $echo .= '<script src="' . $jsPath . '/js/' . $jsFile . '.js?v=' . $webVersion . '" type="text/javascript"></script>';
        }
        return $echo;
    }
    /**
     * 加载Meta头信息
     *
     * @param array $metaVars
     * @return html string
     */
    public static function loadMetaInfo($metaVars){
        include_once RESOURCE_PATH . 'meta/config.php';
        
        $module = Core_Router::getModule();
        $controller = Core_Router::getController();
        $action = Core_Router::getAction();
        
        $keyM = $module;
        $keyMC = $module . '|' . $controller;
        $keyMCA = $module . '|' . $controller . '|' . $action;
        
        if(isset($GLOBALS['metaConfig'][$keyMCA])){
            $meta = $GLOBALS['metaConfig'][$keyMCA];
        }elseif(isset($GLOBALS['metaConfig'][$keyMC])){
            $meta = $GLOBALS['metaConfig'][$keyMC];
        }elseif(isset($GLOBALS['metaConfig'][$keyM])){
            $meta = $GLOBALS['metaConfig'][$keyM];
        }else{
            $meta = $GLOBALS['metaConfig']['default'];
        }
        
        // 替换页头标题中的变量
        $title = '';
        if(isset($meta[0])){
            if(!$metaVars){
                $title = $meta[0];
            }else{
                $keys = array_keys($metaVars);
                foreach($keys as &$key){
                    $key = '{' . $key . '}';
                }
                $title = str_replace($keys, array_values($metaVars), $meta[0]);
            }
        }
        
        return array('title'=>$title, 'keywords'=>isset($meta[1]) ? $meta[1] : '', 'description'=>isset($meta[2]) ? $meta[2] : '');
    }
}