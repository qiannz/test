<?php

/**
 * 分页类
 *
 */

class Custom_Page {
    /**
     * 底部分页条
     *
     * @param int       $total        总记录数
     * @param int       $pageSize     每页几条
     * @param int       $curPage      当前页码
     * @param string    $mpurl        基本URL
     * @param array     $params       附加参数
     * @param int       $adjacents    相邻页码按钮数
     * @param string    $auchor       跳转后定位锚点
     * @return string
     */
    public static function get($total, $pageSize, $curPage, $mpurl, $params = array(), $adjacents = 3, $auchor = ''){
        if($auchor){
            $auchor = '#' . $auchor;
        }      
        $multipage = '';
        //$mpurl = self::setMpUrl($mpurl, $params);
        $realpages = 1;
        if($mpurl[strlen($mpurl) - 1] != '/') {
        	$mpurl .= '/';
        }
        
        if($total > $pageSize){
            $realpages = @ceil($total / $pageSize);
            $maxpage = 10000;
            $pages = $maxpage && $maxpage < $realpages ? $maxpage : $realpages;
            //首页
            $multipage .= '<a href="javascript:page(\'' . $mpurl . 'page/1' . $auchor . '\')" class="first" title="首页">首页</a>';
            // 上一页
            if($curPage > 1){
                $multipage .= '<a href="javascript:page(\'' . $mpurl . 'page/' . ($curPage - 1) . $auchor . '\')" title="上一页" class="pre">上一页</a>';
            }
            
            $multipage .= '<p class="num">';
            // 第一页
            if($curPage > ($adjacents + 1)){
                $multipage .= '<a href="javascript:page(\'' . $mpurl . $auchor . '\')">1</a>';
            }
            
            // 间隔
            if($curPage > ($adjacents + 2)){
                $multipage .= '<b>...</b>';
            }
            
            $from = ($curPage > $adjacents) ? ($curPage - $adjacents) : 1;
            $to = ($curPage < ($pages - $adjacents)) ? ($curPage + $adjacents) : $pages;
            for($i = $from; $i <= $to; $i++){
                if($i == $curPage){
                    $multipage .= '<a class="on">' . $i . '</a>';
                }else{
                    $multipage .= '<a href="javascript:page(\'' . $mpurl . 'page/' . $i . $auchor . '\')" title="' . $i . '">' . $i . '</a>';
                }
            }
            
            // 间隔
            if($curPage < ($pages - $adjacents - 1)){
                $multipage .= '<b>...</b>';
            }
            
            // 最后一页
            if($curPage < ($pages - $adjacents)){
                $multipage .= '<a href="javascript:page(\'' . $mpurl . 'page/' . $pages . $auchor . '\')" title="' . $pages . '">' . $pages . '</a>';
            }
            
            $multipage .= '</p>';
            
            // 下一页
            if($curPage < $pages){
                $multipage .= '<a href="javascript:page(\'' . $mpurl . 'page/' . ($curPage + 1) . $auchor . '\')" title="下一页" class="next">下一页</a>';
                //尾页
            	$multipage .= '<a href="javascript:page(\'' . $mpurl . 'page/'.$pages . $auchor . '\')" class="last" title="尾页">尾页</a>';
            }
            $multipage = $multipage ? $multipage : '';
        }
        return $multipage;
    }
    
    public static function getPageStr($total, $pageSize, $curPage, $mpurl, $params = array(), $adjacents = 3, $auchor = ''){
    	if($auchor){
    		$auchor = '#' . $auchor;
    	}
    
    	$multipage = '';
    	//$mpurl = self::setMpUrl($mpurl, $params);
    	$realpages = 1;
    
    	if($total > $pageSize){
    		$realpages = @ceil($total / $pageSize);
    		$maxpage = 10000;
    		$pages = $maxpage && $maxpage < $realpages ? $maxpage : $realpages;
    		//首页
    		$multipage .= '<span><a href="' . $mpurl . '_1' . $auchor . '" title="首页">首页</a></span>';
    		// 上一页
    		if($curPage > 1){
    			$multipage .= '<span><a href="' . $mpurl . '_' . ($curPage - 1) . $auchor . '" title="上一页" class="pre">上一页</a></span>';
    		}
    
    		// 第一页
    		if($curPage > ($adjacents + 1)){
    			$multipage .= '<a href="' . $mpurl . $auchor . '">1</a>';
    		}
    
    		// 间隔
    		if($curPage > ($adjacents + 2)){
    			$multipage .= '<b>...</b>';
    		}
    
    		$from = ($curPage > $adjacents) ? ($curPage - $adjacents) : 1;
    		$to = ($curPage < ($pages - $adjacents)) ? ($curPage + $adjacents) : $pages;
    		for($i = $from; $i <= $to; $i++){
    			if($i == $curPage){
    				$multipage .= '<a class="selbg">' . $i . '</a>';
    			}else{
    				$multipage .= '<a href="' . $mpurl . '_' . $i . $auchor . '" title="' . $i . '">' . $i . '</a>';
    			}
    		}
    
    		// 间隔
    		if($curPage < ($pages - $adjacents - 1)){
    			$multipage .= '<b>...</b>';
    		}
    
    		// 最后一页
    		if($curPage < ($pages - $adjacents)){
    			$multipage .= '<a href="' . $mpurl . '_' . $pages . $auchor . '" title="' . $pages . '">' . $pages . '</a>';
    		}
    
    		// 下一页
    		if($curPage < $pages){
    			$multipage .= '<span><a href="' . $mpurl . '_' . ($curPage + 1) . $auchor . '" title="下一页" class="next">下一页</a></span>';
    			//尾页
    			$multipage .= '<span><a href="' . $mpurl . '_'.$pages . $auchor . '" title="尾页">尾页</a></span>';
    		}
    		$multipage .= '<span class="topage">到第<input type="text" id="jump_page" value="'.$curPage.'">页</span><a class="go" href="javascript:jumpPage()">go</a>';
    		$multipage = $multipage ? $multipage : '';
    	}
    	return $multipage;
    }

    public static function getSimple($total, $pageSize, $curPage, $mpurl, $adjacents = 3){

        $multipage = '';
        $realpages = 1;
        
        if($total > $pageSize){
            $realpages = @ceil($total / $pageSize);
            $maxpage = 100;
            $pages = $maxpage && $maxpage < $realpages ? $maxpage : $realpages;
            
            
            // 上一页
            /*
            if($curPage > 1) {
            	//$multipage .= '<span><a href="javascript:page(\'' . $mpurl . 'page/' . ($curPage - 1) . '\')" title="Pre">&lt;&lt;</a></span>';
            	$multipage .= '<span><a href="javascript:page(\'' . $mpurl . 'page/' . ($curPage - 1) . '\')" title="上一页">上一页</a></span>';
            } 
            */          
            
            // 第一页
            if($curPage > ($adjacents + 1)){
                $multipage .= '<a href="javascript:page(\'' . $mpurl  . 'page/1\')">1</a>';
            }
            
            // 间隔
            if($curPage > ($adjacents + 2)){
                $multipage .= '<b>...</b>';
            }
            
            $from = ($curPage > $adjacents) ? ($curPage - $adjacents) : 1;
            $to = ($curPage < ($pages - $adjacents)) ? ($curPage + $adjacents) : $pages;
            for($i = $from; $i <= $to; $i++){
                if($i == $curPage){
                    $multipage .= '<a class="selbg">' . $i . '</a>';
                }else{
                    $multipage .= '<a href="javascript:page(\'' . $mpurl . 'page/' . $i  . '\')" title="' . $i . '">' . $i . '</a>';
                }
            }
            
            // 间隔
            if($curPage < ($pages - $adjacents - 1)){
                $multipage .= '<b>...</b>';
            }
            
            // 最后一页
            if($curPage < ($pages - $adjacents)){
                $multipage .= '<a href="javascript:page(\'' . $mpurl . 'page/' . $pages  . '\')" title="' . $pages . '">' . $pages . '</a>';
            }
            
            // 下一页
            /*
            if($curPage < $pages){
                $multipage .= '<span><a href="javascript:page(\'' . $mpurl . 'page/' . ($curPage + 1) . '\')">下一页</a></span>';
                //$multipage .= '<span><a href="javascript:page(\'' . $mpurl . 'page/' . ($curPage + 1) . '\')">&gt;&gt;</a></span>';
            }
            */
            $multipage = $multipage ? $multipage : '';           
        }
        return $multipage;
    }    
    /**
     * 右上角小分页条
     *
     * @param int       $total        总记录数
     * @param int       $pageSize     每页几条
     * @param int       $curPage      当前页码
     * @param string    $mpurl        基本URL
     * @param array     $params       附加参数
     * @param int       $hasPrefix    分页条左侧是否有前缀文字
     * @param string    $auchor       跳转后定位锚点
     * @return string
     */
    public function getSmall($total, $pageSize, $curPage, $mpurl, $params = array(), $hasPrefix = false, $auchor = ''){
        if($auchor){
            $auchor = '#' . $auchor;
        }
        
        $multipage = '';
        $mpurl = self::setMpUrl($mpurl, $params);
        $realpages = 1;
        
        if($total > $pageSize){
            $realpages = @ceil($total / $pageSize);
            $maxpage = 10000;
            $pages = $maxpage && $maxpage < $realpages ? $maxpage : $realpages;
            
            $multipage = ($curPage < $pages ? '<a href="' . $mpurl . 'page=' . ($curPage + 1) . $auchor . '" title="下一页" class="next-arrow r">下一页</a>' : '');
            $multipage .= ($curPage > 1 ? '<a href="' . $mpurl . 'page=' . ($curPage - 1) . $auchor . '" title="上一页" class="no-arrow r">上一页</a>' : '');
            
            $multipage .= $pages ? '<span class="cp-list-total r">' . $curPage . '/' . $pages . '页</span>' : '';
            $hasPrefix ? $multipage .= '<span>共<em> ' . $total . '款</em></span> <span>每页' . $pageSize . '款</span>' : '';
            
            $multipage = $multipage ? $multipage : '';
        }
        return $multipage;
    }
    
    /**
     * 右上角小分页条
     *
     * @param int       $total        总记录数
     * @param int       $pageSize     每页几条
     * @param int       $curPage      当前页码
     * @param string    $mpurl        基本URL
     * @param array     $params       附加参数
     * @param string    $objName      选择器对象名
     * @return string
     */
    public function getPic($total, $pageSize, $curPage, $mpurl, $params = array(), $objName = NULL){
        $multipage = '';
        $mpurl = self::setMpUrl($mpurl, $params);
        $realpages = 1;
        
        if($total > $pageSize){
            $realpages = @ceil($total / $pageSize);
            $maxpage = 10000;
            $pages = $maxpage && $maxpage < $realpages ? $maxpage : $realpages;
            
            if(NULL === $objName){
                $multipage = ($curPage > 1 ? '<a href="' . $mpurl . 'page=' . ($curPage - 1) . '" title="前一张" class="scroll-prev" >前一张</a>' : '');
                $multipage .= '<ul>';
                for($i = 1; $i <= $pages; $i++){
                    $multipage .= $i == $curPage ? '<li><a href="' . $mpurl . 'page=' . $i . '" title="" class="cur">' . $i . '</a></li>' : '<li><a href="' . $mpurl . 'page=' . $i . '" title="">' . $i . '</a></li>';
                }
                $multipage .= '</ul>';
                $multipage .= ($curPage < $pages ? '<a href="' . $mpurl . 'page=' . ($curPage + 1) . '" title="后一张" class="scroll-next">后一张</a>' : '');
            }else{
                $multipage = ($curPage > 1 ? '<a onclick="goPage(\'' . $objName . '\',' . ($curPage - 1) . ');" href="javascript:;" title="前一张" class="scroll-prev" >前一张</a>' : '');
                $multipage .= '<ul>';
                for($i = 1; $i < $pages; $i++){
                    $multipage .= $i == $curPage ? '<li><a onclick="goPage(\'' . $objName . '\',' . $i . ');" href="javascript:;" title="" class="cur">' . $i . '</a></li>' : '<li><a onclick="goPage(\'' . $objName . '\',' . $i . ');" href="javascript:;" title="">' . $i . '</a></li>';
                }
                $multipage .= '</ul>';
                $multipage .= ($curPage < $pages ? '<a onclick="goPage(\'' . $objName . '\',' . ($curPage + 1) . ');" href="javascript:;" title="后一张" class="scroll-next">后一张</a>' : '');
            }
            $multipage = $multipage ? $multipage : '';
        }
        return $multipage;
    }
    
    /**
     * 根据条件组合生成 URL
     *
     * @param string $mpurl 基本URL
     * @param array $params 附加参数
     * @return string
     */
    public static function setMpUrl($mpurl, $params = array()){
        if(strpos($mpurl, '?') === false && substr($mpurl, -1, 1) != '/'){
            $mpurl .= '/';
        }
        $mpurl .= (strpos($mpurl, '?') === false ? '?' : '&') . ($params ? http_build_query($params) . '&' : '');
        return $mpurl;
    }
}