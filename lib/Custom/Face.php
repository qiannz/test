<?php

/**
 * 表情函数库
 *
 */

class Custom_Face {
    /**
     * 替换表情bbcode到htmlCode
     *
     * @param string $faceCode
     */
    public static function faceCodeToHtml($faceCode){
        //$faceCode = preg_replace("/\[em:(\d+):]/is", "<img src=\"/img/face/face/\\1.gif\" class=\"face\">", $faceCode);
        //$faceCode = preg_replace("/\<br.*?\>/is", ' ', $faceCode);
        

        $strPreg = "/\[(.*?)\]/u";
        $faceCode = preg_replace_callback($strPreg, array('self', '_chechFace'), $faceCode);
        
        return $faceCode;
    }
    
    public function _chechFace($matches){
        $str = '[' . $matches[1] . ']';
        $faceTxt = array('呆滞', '微笑', '大笑', '嘻嘻', '优雅', '生气', '纠结', '郁闷', '流泪', '懒得理你', '晕', '汗', '困', '害羞', '惊讶', '高兴', '爱你', '酷', '石化', '囧', '睡觉', '调皮', '亲亲', '疑惑', '闭嘴', '难过', '无语', '加油', '鄙视', '猪', '骷髅', '衰', '偷笑', '委屈', '快哭了');
        $strFaceId = array_search($matches[1], $faceTxt);
        if($strFaceId !== false){
            $str = "<img src=\"/img/face/face/" . ($strFaceId + 1) . ".gif\" class=\"face\">";
        }
        
        return $str;
    }
    
    /**
     * 替换表情htmlCode到bbcode
     *
     * @param string $faceCode
     */
    public static function htmlToFaceCode($htmlCode){
        $bbCode = preg_replace("/[ \t]*\<img src=\"\/img\/face\/face\/(.+?).gif\".*?\>[ \t]*/is", "[em:\\1:]", $htmlCode);
        return $bbCode;
    }
}