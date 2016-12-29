<?php

/**
 *
 * Date: 16-12-12
 * Time: 上午12:37
 * author :李华 yehong0000@163.com
 */
namespace App\Library\Transform;
use App\Library\Transform\Attachment\Image;
use App\Library\Transform\Attachment\File;
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
    //list类型
    private $listType;
    //list信息暂存
    private $listTmp;
    //是否将样式插入节点
    private $isInsertCssIntoNode;
    //附件保存路径
    private $attachmentPath;
    //标签映射
    private static $labelMapping = [
        'para' => 'div',
        'text' => 'div'
    ];
    //标签类映射
    private static $labelClassMapping = [
        'para' => 'para',//块
        'text' => 'text',//文办
        'list' => 'list',//列表
        'hr'   => 'horizontal-line',//分割线
    ];
    //样式映射
    private static $cssMapping = [
        'bold'         => 'font-weight',
        'italic'       => 'font-style',
        'underline'    => 'text-decoration',
        'line-through' => 'text-decoration',
        'strike'       => ['text-decoration' => 'line-through'],
        'back-color'   => 'background-color',
        'align'        => 'text-align',
        'textColor'    => 'color',
        'backColor'    => 'background-color',
        'indent'       => 'text-indent'
    ];

    //列表序号类型,按照level层级，循环取值
    private $listStyleType = [
        'ul' => ['disc', 'circle', 'square'],
        'ol' => ['decimal', 'lower-alpha', 'lower-roman']
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
        $c = $this->HtmlXml->addChild('link');
        $c->addAttribute('rel', 'stylesheet');
        $c->addAttribute('type', 'text/css');
        $c->addAttribute('href', 'note.css');
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
        $tmp = [];
        for ($Xml->rewind(); $Xml->valid(); $Xml->next()) {
            foreach ($Xml->current()->attributes() as $k => $v) {//遍历节点属性
                $tmp[(string)$k] = (string)$v;
            }
            $this->listType[] = $tmp;
            $tmp = [];
        }
        $this->listType = array_column($this->listType, 'type', 'id');
    }

    /**
     * @param \SimpleXMLElement $Xml
     */
    private function body(\SimpleXMLIterator $Xml) {
        if ($Xml->getName() != 'body') {
            throw new \Exception('XML分析失败!', 4003);
        }
        for ($Xml->rewind(); $Xml->valid(); $Xml->next()) {
            call_user_func([$this, strtoupper(str_replace('-', '_', $Xml->current()->getName()))], $Xml->current());
        }
        echo $this->HtmlXml->asXml();
    }


    /**
     * 文字块解析
     *
     * @param \SimpleXMLIterator $Xml
     */
    private function PARA(\SimpleXMLIterator $Xml) {
        $replace = isset(self::$labelMapping['para']) ? self::$labelMapping['para'] : 'div';
        $id = property_exists($Xml, 'coId') ? $Xml->coId : uniqid();
        $class = isset(self::$labelClassMapping['para']) ? self::$labelClassMapping['para'] : 'para';
        if (property_exists($Xml, 'styles') && ($Xml->styles instanceOf \SimpleXMLIterator)) {
            $paraCss = $this->styles($Xml->styles);
            $paraCss['white-space'] = 'pre-wrap';
        }
        if (property_exists($Xml, 'text')) {
            if (!$Xml->text->__toString()) {
                $currentXml = $this->HtmlXml->addChild($replace, ' ');
                $currentXml->addChild('br');
            } else {
                $this->combinationTextAndStyle($Xml, $this->HtmlXml, $replace, $id, $class, $paraCss);
            }
        }
    }

    private function LIST_ITEM(\SimpleXMLIterator $Xml) {
        $attr = [];
        foreach ($Xml->attributes() as $k => $v) {//遍历节点属性
            $attr[(string)$k] = (string)$v;
        }
        $replace = 'ul';
        $listID = isset($attr['list-id']) ? $attr['list-id'] : uniqid();
        $level = isset($attr['level']) ? $attr['level'] : 1;
        if (isset($this->listType[$listID])) {
            if ($this->listType[$listID] == 'unordered') {
                $replace = 'ul';
            } elseif ($this->listType[$listID] == 'ordered') {
                $replace = 'ol';
            }
        }
        $id = property_exists($Xml, 'coId') ? $Xml->coId : uniqid();
        $class = isset(self::$labelClassMapping['list']) ? self::$labelClassMapping['list'] : 'list';
        if (property_exists($Xml, 'styles') && ($Xml->styles instanceOf \SimpleXMLIterator)) {
            $paraCss = $this->styles($Xml->styles);
        }
        if (isset($this->listTmp[$listID][$level])) {
            $HtmlXml = $this->listTmp[$listID][$level];
        } else {
            if (isset($this->listTmp[$listID][$level - 1])) {
                $HtmlXml = $this->listTmp[$listID][$level - 1]->addChild($replace);
            } else {
                $HtmlXml = $this->HtmlXml->addChild($replace);
            }
            $HtmlXml->addAttribute('list-style-type', $this->getListStyleType($replace, $level));
            $this->listTmp[$listID][$level] = $HtmlXml;
        }
        if (property_exists($Xml, 'text') && $Xml->text) {
            $this->combinationTextAndStyle($Xml, $HtmlXml, 'li', $id, $class, $paraCss);
        }
    }

    /**
     * 分割线
     *
     * @param \SimpleXMLIterator $Xml
     */
    private function HORIZONTAL_LINE(\SimpleXMLIterator $Xml) {
        $replace = 'hr';
        $id = property_exists($Xml, 'coId') ? $Xml->coId : uniqid();
        $class = isset(self::$labelClassMapping['hr']) ? self::$labelClassMapping['hr'] : ['hr'];
        $HtmlXml = $this->HtmlXml->addChild($replace);
        $HtmlXml->addAttribute('id', $id);
        $HtmlXml->addAttribute('class', $class);
        $HtmlXml->addAttribute('style', 'clear:both;');
    }

    /**
     * 表格
     *
     * @param \SimpleXMLIterator $Xml
     */
    private function TABLE(\SimpleXMLIterator $Xml) {
        $id = property_exists($Xml, 'coId') ? $Xml->coId : uniqid();
        $class = isset(self::$labelClassMapping['table']) ? self::$labelClassMapping['table'] : 'table';
        $content = json_decode($Xml->content, true);
        $tableXml = $this->HtmlXml->addChild('table');
        $tabCss = [
            'table-layout'    => 'fixed',
            'border-collapse' => 'collapse',
            'border'          => '1px solid #ccc'
        ];
        if (isset($content['widths']) && $width = array_sum($content['widths'])) {
            $tabCss['width'] = $width;
        }
        $css = $this->getCssStr($tabCss, $id);
        $tableXml->addAttribute('id', $id);
        $tableXml->addAttribute('class', $class);
        if ($this->isInsertCssIntoNode) {
            $tableXml->addAttribute('style', $css['css']);
        }
        $tBodyXml = $tableXml->addChild('tbody');
        if (count($content['cells']) == count($content['widths']) * count($content['heights'])) {
            $tds = count($content['widths']);
        } else {
            $tds = 1;
        }
        $cells = array_chunk($content['cells'], $tds);
        foreach ($cells as $sK => $sV) {
            $trXml = $tBodyXml->addChild('tr');
            foreach ($sV as $k => $v) {
                $tdCss = [
                    'word-wrap' => 'break-word'
                ];
                if (isset($content['widths'][$k])) {
                    $tdCss['width'] = $content['widths'][$k];
                }
                if (isset($content['heights'][$sK])) {
                    $tdCss['height'] = $content['heights'][$sK];
                }
                $value = $v['value'];
                unset($v['value']);
                foreach ($v as $key => $val) {
                    $cssMap = $this->cssMap($key);
                    $tdCss[$cssMap['key']] = isset($cssMap['valueForce']) ? $cssMap['valueForce'] : $val;
                }
                $tdXml = $trXml->addChild('td', $value ?: '');
                $css = $this->getCssStr($tdCss, $id, '_td_' . $k);
                $tdXml->addAttribute('id', $css['select']);
                if ($this->isInsertCssIntoNode) {
                    $tdXml->addAttribute('style', $css['css']);
                }
            }
        }
    }

    /**
     * 图片
     *
     * @param \SimpleXMLIterator $Xml
     */
    private function IMAGE(\SimpleXMLIterator $Xml) {
        $id = property_exists($Xml, 'coId') ? $Xml->coId : uniqid();
        $source=$Xml->source->__toString();
        $image=$this->HtmlXml->addChild('img');
        $image->addAttribute('id',$id);
        $imageCss=[
            'cursor'=>'pointer'
        ];
    }

    /**
     * 文本样式
     *
     * @param \SimpleXMLIterator $Xml
     * @param \SimpleXMLIterator $HtmlXml
     * @param $replace
     * @param $id
     * @param $class
     * @param $paraCss
     */
    private function combinationTextAndStyle(\SimpleXMLIterator $Xml, \SimpleXMLIterator $HtmlXml, $replace, $id, $class, $paraCss) {
        $lineCss = [];
        $paraCssStr = $this->getCssStr($paraCss, $id);
        if (property_exists($Xml, 'inline-styles') && ($Xml->{"inline-styles"} instanceOf \SimpleXMLIterator)) {
            $lineCss = $this->lineStyles($Xml->{"inline-styles"});
        }
        if ($lineCss) {
            $currentHtmlChild = $HtmlXml->addChild($replace);
            if ($this->isInsertCssIntoNode) {
                $currentHtmlChild->addAttribute('style', $paraCssStr['css']);
            }
            $currentHtmlChild->addAttribute('id', $paraCssStr['select']);
            $currentHtmlChild->addAttribute('class', $class);
            $lineCss = $this->stylesSort($lineCss, $Xml->text);
            if ($lineCss['com']) {
                $currentHtmlChild = $currentHtmlChild->addChild('div', '');
                isset(self::$labelMapping['hr']) ? self::$labelMapping['hr'] : 'hr';
                $lineCssStr = $this->getCssStr($lineCss['com'], $id);
                $currentHtmlChild->addAttribute('id', $lineCssStr['select']);
                if ($this->isInsertCssIntoNode) {
                    $currentHtmlChild->addAttribute('style', $lineCssStr['css']);
                }
            }
            foreach ($lineCss['line'] as $k => $v) {
                $lineCssStr = $this->getCssStr($v, $id);
                if ($lineCssStr['href']) {
                    $currentChild = $currentHtmlChild->addChild('a', $v['text'] ?: ' ');
                    $currentChild->addAttribute('href', $lineCssStr['href']);
                } else {
                    $currentChild = $currentHtmlChild->addChild('span', $v['text'] ?: ' ');
                }
                if ($this->isInsertCssIntoNode) {
                    $currentChild->addAttribute('style', $lineCssStr['css']);
                }
                $currentChild->addAttribute('id', $lineCssStr['select']);
            }
        } else {
            $currentHtmlChild = $HtmlXml->addChild($replace, $Xml->text);
            if ($this->isInsertCssIntoNode) {
                $currentHtmlChild->addAttribute('style', $paraCssStr['css']);
            }
            $currentHtmlChild->addAttribute('id', $paraCssStr['select']);
            $currentHtmlChild->addAttribute('class', $class);
        }
    }

    /**
     * 获取列表样式类型
     *
     * @param $type
     * @param $level
     * @return mixed
     */
    private function getListStyleType($type, $level) {
        $listStyleType = isset($this->listStyleType[$type]) ? $this->listStyleType[$type] : ['none'];
        $count = count($listStyleType);
        $num = $level % $count;
        if ($num == 0) {
            $key = $count;
        } else {
            $key = $num;
        }
        $key--;
        return $listStyleType[$key];
    }

    /**
     * 获取样式字符
     *
     * @param $css
     * @param $id
     * @return array
     */
    private function getCssStr($css, $id) {
        if (isset($css['x']) && isset($css['y'])) {
            $id = $id . '_' . $css['x'] . '_' . $css['y'];
        }
        $id = '#' . $id;
        $cssStr = '';
        if (isset($css['styles'])) {
            $css = $css['styles'];
        }
        $href = '';
        foreach ($css as $k => $v) {
            if ($k == 'href') {
                $href = $v;
                continue;
            }
            $this->cssTmp[$id][] = $k . ': ' . $v;
            $cssStr .= $k . ': ' . $v . ';';
        }
        return [
            'css'    => $cssStr,
            'select' => $id,
            'href'   => $href
        ];
    }

    /**
     * 排序
     * @param $lineCss
     * @return array
     */
    private function stylesSort($lineCss, $text) {
        $lineCss = array_values($lineCss);
        $tmp = [];
        $count = count($lineCss);
        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $tmp = $lineCss[$i];
                if ($lineCss[$i]['y'] > $lineCss[$j]['y']) {
                    $lineCss[$i] = $lineCss[$j];
                    $lineCss[$j] = $tmp;
                } elseif ($lineCss[$i]['y'] == $lineCss[$j]['y'] && $lineCss[$i]['x'] < $lineCss[$j]['x']) {
                    $lineCss[$i] = $lineCss[$j];
                    $lineCss[$j] = $tmp;
                }
            }
        }
        //合并样式
        $filterCss = ['href' => ''];//不允许合并的样式
        for ($i = 0; $i < $count; $i++) {
            $lineCss[$i]['styles'] = array_merge($filterCss, $lineCss[$i]['styles']);
            for ($j = $i + 1; $j < $count; $j++) {
                if ($lineCss[$i]['x'] >= $lineCss[$j]['x'] && $lineCss[$i]['y'] <= $lineCss[$j]['y']) {
                    $lineCss[$i]['styles'] = array_merge($lineCss[$j]['styles'], $lineCss[$i]['styles']);
                }
            }
            $lineCss[$i]['styles'] = array_filter($lineCss[$i]['styles']);
        }
        //字符起切割分配
        $tmp = $com = [];
        $len = mb_strlen($text);
        $start = 0;//切割初始位置
        foreach ($lineCss as $k => $v) {
            if ($v['x'] > $start && isset($lineCss[$k + 1])) {
                $tmp[] = [
                    'text' => mb_substr($text, $start, $v['x'] - $start)
                ];
            } else {
                $v['text'] = mb_substr($text, $start, $v['y'] - $start);
                $tmp[] = $v;
            }
            $start = $v['y'];
        }
        $end = end($tmp);
        if ($end['y'] - $end['x'] == $len && $end['text'] == '') {
            $com = $end;
            array_pop($tmp);
        }
        return [
            'line' => $tmp,
            'com'  => $com
        ];
    }

    /**
     * 行css解析
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
            $css = $this->cssMap($child->getName());
            if ($child->value->__toString() == 'true') {
                $cssTmp[$key]['styles'][$css['key']] = isset($css['value']) ? $css['value'] : $child->getName();
            } else {
                $cssTmp[$key]['styles'][$css['key']] = $child->value->__toString();
            }
            $cssTmp[$key]['x'] = $x;
            $cssTmp[$key]['y'] = $y;
        }
        return $cssTmp;
    }

    /**
     * 块样式解析
     *
     * @param \SimpleXMLIterator $Xml
     * @return array
     */
    private function styles(\SimpleXMLIterator $Xml) {
        $cssTmp = [];
        foreach ($Xml->children() as $key => $child) {
            $css = $this->cssMap($child->getName());
            $cssTmp[$css['key']] = isset($css['valueForce']) ? $css['valueForce'] : $child->__toString();
        }
        return $cssTmp;
    }

    /**
     * 一些value为true的css映射
     * @param $css
     * @return mixed
     */
    private function cssMap($css) {
        if (isset(self::$cssMapping[$css])) {
            if (is_array(self::$cssMapping[$css])) {
                return ['valueForce' => reset(self::$cssMapping[$css]), 'key' => key(self::$cssMapping[$css])];
            } else {
                return ['key' => self::$cssMapping[$css], 'value' => $css];
            }
        } else {
            self::log('css mapping not find:' . "\t" . $css);
            return ['key' => $css, 'value' => null];
        }
    }

    /**
     * 记录转化过程中出现的一些错误以供分析
     * @param $str
     * @return boolean
     */
    public static function log($str) {
        $path = __DIR__ . '/' . 'log/';
        if (!is_dir($path)) {
            mkdir($path);
        }
        $fileName = $path . date('Ymd') . '.log';
        $str = date('Y-m-d H:i:s') . "\t" . $str . "\n";
        return file_put_contents($fileName, $str, FILE_APPEND);
    }

    /**
     * 无法解析的标签
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments) {
        self::log($name . "\t" . json_encode($arguments));
    }

}

Main::getInstance()->transform(null);