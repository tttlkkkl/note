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
    private static $labelMapping=[
        'para'=>'div',
        'text'=>'div'
    ];
    //标签类映射
    private static $labelClassMapping=[
        'para'=>'para',
        'text'=>'text'
    ];
    //样式映射
    private static $cssMapping=[
        'bold'=>'font-weight',
        'italic'=>'font-style',
        'underline'=>'text-decoration',
        'line-through'=>'text-decoration'
    ];

    private function __construct() {
        $this->isInsertCssIntoNode=true;
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
        $Xml=simplexml_load_file('../../../note.xml');
        if ($Xml === false) {
            throw new \Exception(libxml_get_last_error(), 4000);
        }
        $this->execute($Xml);
    }

    private function execute(\SimpleXMLElement $Xml) {
        $this->HtmlXml=new \SimpleXMLIterator("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?><div id='article-warp'></div>");
        $Xml=new \SimpleXMLIterator($Xml->asXML());

        foreach($Xml->children() as $child){
            if($child->getName() == 'head'){
                $this->head($child);
            }
            if($child->getName()=='body'){
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
        if($Xml->getName() != 'body'){
            throw new \Exception('XML分析失败!',4003);
        }
        for ($Xml->rewind();$Xml->valid();$Xml->next()){
            call_user_func([$this,$Xml->current()->getName()],$Xml->current());
        }
    }

    private function para(\SimpleXMLIterator $Xml){
        $replace=self::$labelMapping['para']?:'div';
        $id=property_exists($Xml,'coId')?$Xml->coId:uniqid();
        $class=self::$labelClassMapping['para']?:['para'];
        $css='white-space: pre-wrap;';

        $this->cssTmp[$css][]=$css;
        if(property_exists($Xml,'text')){
            if(!$Xml->text){
                $this->HtmlXml->addChild($replace,' ');
                $this->HtmlXml->children()->addChild('br');
            }else{
                $this->HtmlXml->addChild($replace,$Xml->text);
            }
        }
        if($this->isInsertCssIntoNode){
            $this->HtmlXml->children()->addAttribute('style',$css);
        }else{
            $this->HtmlXml->children()->addAttribute('id',$id);
            $this->HtmlXml->children()->addAttribute('class',$class);
        }
        if(property_exists($Xml,'inline-styles') && ($Xml instanceOf \SimpleXMLIterator)){
            $this->lineStyle($Xml->{"inline-styles"});
        }
        echo $this->HtmlXml->asXml();
        die;
    }

    private function lineStyle(\SimpleXMLIterator $Xml){
        foreach ($Xml->children() as $key => $child){
            echo $child->getName()."\n";
        }
    }

    /**
     * 记录转化过程中出现的一些错误以供分析
     * @param $str
     * @return boolean
     */
    private function log($str){
        $path=__DIR__.'/'.'log/';
        if(!is_dir($path)){
            mkdir($path);
        }
        $fileName=$path.date('Ymd').'.log';
        $str=date('Y-m-d H:i:s')."\t".$str;
        return file_put_contents($fileName,$str,FILE_APPEND);
    }

    /**
     * 无法解析的标签
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        $this->log($name."\t".json_encode($arguments));
    }

}

Main::getInstance()->transform(null);