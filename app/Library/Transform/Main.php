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
    //是否将样式插入节点
    private $insertCssIntoNode;
    //标签映射
    private static $labelMapping=[
        'para'=>'div',
        'text'=>'div'
    ];
    //样式映射
    private static $cssMapping=[
        'bold'=>'font-weight',
        'italic'=>'font-style',
        'underline'=>'text-decoration',
        'line-through'=>'text-decoration'
    ];

    private function __construct() {
        $this->insertCssIntoNode=true;
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
        $this->HtmlXml=new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?><div id='article-warp'></div>");
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
            echo $Xml->current()->getName(),"\n";
        }
        foreach($Xml->children() as $key=>$child){
            if(array_key_exists($key,self::$labelMapping)){
                $this->HtmlXml->addChild(self::$labelMapping[$key],' ');
                if(property_exists($child,'coId')){
                    $this->HtmlXml->children()->addAttribute('id',$child->coId);
                }
                if(property_exists($child,'text')){
                    if($child->text){
                        $this->HtmlXml->children()->addChild('div',$child->text);
                    }else{
                        $this->HtmlXml->children()->addChild('br');
                    }
                }
            }
            echo $this->HtmlXml->asXML();
            break;
        }
    }

}

Main::getInstance()->transform(null);