<?php

/**
 *
 * Date: 16-12-12
 * Time: 上午12:37
 * author :李华 yehong0000@163.com
 */
namespace App\Library\Transform;
class Main {
    protected static $Obj;
    //HTML文档
    private $HtmlXml;
    //生成的样式
    private $css;
    //头部
    private $Head;
    //主体
    private $Body;
    //css文件暂存数组['key'=>'','style'=>[]]
    private $cssTmp;
    //是否将样式插入节点
    private $isInsertCssIntoNode;
    //标签映射
    private static $labelMapping = [
        'para' => 'div',
        'text' => 'div'
    ];
    //标签类映射
    private static $labelClassMapping = [
        'para' => 'para',
        'text' => 'text'
    ];
    //样式映射
    private static $cssMapping = [
        'bold'         => 'font-weight',
        'italic'       => 'font-style',
        'underline'    => 'text-decoration',
        'line-through' => 'text-decoration',
        'align'        => 'text-align'
    ];

    private function __construct() {
        $this->isInsertCssIntoNode = true;
    }

    /**
     * 获取实例
     * @return Main
     */
    public static function getInstance() {
        if (!self::$Obj) {
            self::$Obj = new self();
        }
        return self::$Obj;
    }

    /**
     * xml 转换为html
     * @param $content
     * @return string
     */
    public function transform($content) {
        //$Xml = simplexml_load_string($content);
        $Xml = simplexml_load_file('../../../note.xml');
        if ($Xml === false) {
            throw new \Exception(libxml_get_last_error(), 4000);
        }
        $this->execute($Xml);
    }

    private function execute(\SimpleXMLElement $Xml) {
        $this->HtmlXml = new \SimpleXMLIterator("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?><div id='article-warp'></div>");
        $Xml = new \SimpleXMLIterator($Xml->asXML());

        foreach ($Xml->children() as $child) {
            if ($child->getName() == 'head') {
                $this->head($child);
            }
            if ($child->getName() == 'body') {
                $this->body($child);
            }
        }
    }

    /**
     * @param \SimpleXMLElement $Xml
     */
    private function head(\SimpleXMLIterator $Xml) {
        //echo $Xml->getName();
    }

    /**
     * @param \SimpleXMLElement $Xml
     */
    private function body(\SimpleXMLIterator $Xml) {
        if ($Xml->getName() != 'body') {
            throw new \Exception('XML分析失败!', 4003);
        }
        for ($Xml->rewind(); $Xml->valid(); $Xml->next()) {
            call_user_func([$this, $Xml->current()->getName()], $Xml->current());
        }
    }

    private function para(\SimpleXMLIterator $Xml) {
        $replace = self::$labelMapping['para'] ?: 'div';
        $id = property_exists($Xml, 'coId') ? $Xml->coId : uniqid();
        $class = self::$labelClassMapping['para'] ?: ['para'];
        $lineCss = $paraCss = [];
        if (property_exists($Xml, 'styles') && ($Xml->styles instanceOf \SimpleXMLIterator)) {
            $paraCss = $this->styles($Xml->styles);
            $paraCss['white-space'] = 'pre-wrap';
        }
        $paraCssStr = '';
        foreach ($paraCss as $k => $v) {
            $this->cssTmp['#' . $id][] = $k . ': ' . $v . ';';
            $paraCssStr .= $k . ': ' . $v . ';';
        }
        if (property_exists($Xml, 'text')) {
            if (!$Xml->text) {
                $this->HtmlXml->addChild($replace, ' ');
                $this->HtmlXml->children()->addChild('br');
            } else {
                if (property_exists($Xml, 'inline-styles') && ($Xml->{"inline-styles"} instanceOf \SimpleXMLIterator)) {
                    $lineCss = $this->lineStyles($Xml->{"inline-styles"});
                }
                if ($lineCss) {
                    $lineCss=$this->stylesSort($lineCss,$Xml->text);
                    print_r($lineCss);
                    die;
                    $len=mb_strlen($Xml->text);
                    $offset=0;//字符串偏移量
                    foreach($lineCss as $k => $v){
                        $this->HtmlXml->addChild($replace, ' ');
                        if($k==0 && $v['x']!=0){
                            $this->HtmlXml->children()->addChild('span',substr($Xml->text,0,$v['x']));
                            $this->HtmlXml->children()->chiledren()->addAttribute('style',implode(';',$v['styles']));
                        }elseif( $k<$len-1){
                            if($v['y'] !=$lineCss[$k+1]['x']){
                                $this->HtmlXml->children()->addChild('span',substr($Xml->text,$v['y'],$lineCss[$k+1]['x']));
                                $this->HtmlXml->children()->children()->addAttribute('style',implode(';',$v['styles']));
                            }else{
                                $this->HtmlXml->children()->addChild('span',substr($Xml->text,0,$v['x']));
                                $this->HtmlXml->children()->children()->addAttribute('style',implode(';',$v['styles']));
                            }
                        }
                        $this->HtmlXml->addChild('span','');
                    }
                } else {
                    $this->HtmlXml->addChild($replace, $Xml->text);
                }
            }
        }
        if ($this->isInsertCssIntoNode) {
            $this->HtmlXml->children()->addAttribute('style', $paraCssStr);
        }
        $this->HtmlXml->children()->addAttribute('id', $id);
        $this->HtmlXml->children()->addAttribute('class', $class);
        //ksort($lineCss);
        //print_r($lineCss);
        //print_r($this->textCssToHtml($lineCss));
        echo $this->HtmlXml->asXml();
        die;
    }

    /**
     * 排序
     * @param $lineCss
     * @return array
     */
    private function stylesSort($lineCss,$text) {
        $lineCss=array_values($lineCss);
        $tmp=[];
        $count=count($lineCss);
        for($i=0;$i<$count;$i++){
            for($j=$i+1;$j<$count;$j++){
                $tmp=$lineCss[$i];
                if($lineCss[$i]['y'] > $lineCss[$j]['y']){
                    $lineCss[$i]=$lineCss[$j];
                    $lineCss[$j]=$tmp;
                }elseif($lineCss[$i]['y']==$lineCss[$j]['y'] && $lineCss[$i]['x']<$lineCss[$j]['x']){
                    $lineCss[$i]=$lineCss[$j];
                    $lineCss[$j]=$tmp;
                }
            }
        }
        //合并样式
        $filterCss=['href'=>''];//不允许合并的样式
        for($i=0;$i<$count;$i++){
            $lineCss[$i]['styles']=array_merge($filterCss,$lineCss[$i]['styles']);
            for($j=$i+1;$j<$count;$j++){
                if($lineCss[$i]['x']>=$lineCss[$j]['x'] && $lineCss[$i]['y']<=$lineCss[$j]['y']){
                    $lineCss[$i]['styles']=array_merge($lineCss[$j]['styles'],$lineCss[$i]['styles']);
                }
            }
            $lineCss[$i]['styles']=array_filter($lineCss[$i]['styles']);
        }
        //字符分配
        for($i=0;$i<$count;$i++){

        }
        return $lineCss;
    }

    /**
     * 行css
     *
     * @param \SimpleXMLIterat11or $Xml
     * @param string $str
     * @return array
     */
    private function lineStyles(\SimpleXMLIterator $Xml) {
        $cssTmp = [];
        foreach ($Xml->children() as $key => $child) {
            $x = $child->from->__toString();
            $y = $child->to->__toString();
            $key = $x . '_' . $y;
            if ($child->value->__toString() == 'true') {
                $cssTmp[$key]['styles'][$this->cssMap($child->getName())] = $child->getName();
            } else {
                $cssTmp[$key]['styles'][$child->getName()] = $child->value->__toString();
            }
            $cssTmp[$key]['x'] = $x;
            $cssTmp[$key]['y'] = $y;
        }
        return $cssTmp;
    }

    /**
     * 块样式
     *
     * @param \SimpleXMLIterator $Xml
     * @return array
     */
    private function styles(\SimpleXMLIterator $Xml) {
        $cssTmp = [];
        foreach ($Xml->children() as $key => $child) {
            $cssTmp[$this->cssMap($child->getName())] = $child->__toString();
        }
        return $cssTmp;
    }

    /**
     * 一些value为true的css映射
     * @param $css
     * @return mixed
     */
    private function cssMap($css) {
        if (self::$cssMapping[$css]) {
            return self::$cssMapping[$css];
        } else {
            $this->log('css:' . "\t" . $css);
            return $css;
        }
    }

    /**
     * 记录转化过程中出现的一些错误以供分析
     * @param $str
     * @return boolean
     */
    private function log($str) {
        $path = __DIR__ . '/' . 'log/';
        if (!is_dir($path)) {
            mkdir($path);
        }
        $fileName = $path . date('Ymd') . '.log';
        $str = date('Y-m-d H:i:s') . "\t" . $str;
        return file_put_contents($fileName, $str, FILE_APPEND);
    }

    /**
     * 无法解析的标签
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments) {
        $this->log($name . "\t" . json_encode($arguments));
    }

}

Main::getInstance()->transform(null);