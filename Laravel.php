<?php

require 'vendor/autoload.php';


use Alfred\Workflows\Workflow;
// $workflow->result()
//          ->uid('bob-belcher')   唯一编号 : STRING (可选)，用于排序
//          ->title('Bob')         标题： STRING， 显示结果
//          ->subtitle('Head Burger Chef')  副标题： STRING ,显示额外的信息
//          ->quicklookurl('http://www.bobsburgers.com')  快速预览地址 : STRING (optional)
//          ->type('default')   类型，可选择文件类型: "default" | "file"
//          ->arg('bob')    输出参数 : STRING (recommended)，传递值到下一个模块
//          ->valid(true)       回车是否可用 : true | false (optional, default = true)
//          ->icon('bob.png')   图标
//          ->mod('cmd', 'Search for Bob', 'search')   修饰键 : OBJECT (可选)
//          ->text('copy', 'Bob is the best!')   按cmd+c 复制出来的文本: OBJECT (optional)
//          ->autocomplete('Bob Belcher');    自动补全 : STRING (recommended)
class Laravel
{
    private $workflow;
    private $result;
    private $query;
    private $historyFile;
    private $version;


    public function __construct($version)
    {
        $this->docs = [
            '5.8' => 132,
            '5.7' => 67,
            '5.6' => 30,
            '5.5' => 29,
            '5.4' => 28,
            '5.3' => 27,
            '5.2' => 26,
            '5.1' => 24,
        ];
        $this->version = $version;
        $this->workflow = new Workflow;
        $this->historyFile = 'Laravel-'. @date('Ym') .'.log';
        $this->url = 'https://learnku.com/docs/laravel/'.$this->version;
    }


    public function query($query)
    {
        $this->query = $query;
        $url = $this->getOpenQueryUrl($query);
        $response = $this->workflow->request($url);
        $this->result   = json_decode($response, true);
        if(empty($this->result)){
            $this->addItem('暂无结果', '暂无结果', $this->url);
        }else{
            $this->parseBasic($this->result);
        }
        return $this->workflow->output();
    }

    /*
     * 解析结果
     */
    private function parseBasic($basics)
    {
        foreach ($basics['results'] as $basic) {
            foreach($basic as $key => $item) {
                if ($key == 'results'){
                    foreach ($item as $item1) {
                        $this->addItem($item1['title'], $item1['description'],$item1['url']);
                    }
                }

            }
        }
    }

    /**
     * 随机从配置中获取一组 keyfrom 和 key
     * @param $title 标题
     * @param $subtitle 副标题
     * @param $arg 传递值
     * @return array
     */
    private function addItem($title, $subtitle, $arg = null, $toArray = false)
    {
        $_icon = 'icon.png';
        $result = $this->workflow->result()
            ->title($title)
            ->subtitle($subtitle)
            ->quicklookurl($arg)
            ->arg($arg)
            ->icon($_icon)
            ->text('copy', $title);

        if ($toArray) {
            return $result->toArray();
        }
    }

    /**
     * 组装接口地址
     * @return String
     */
    private function getOpenQueryUrl($query)
    {
        $version_id = $this->docs[(string)$this->version];
        $api = 'https://learnku.com/books/api_search/'.$version_id.'/?is_docs=yes&user_id=0&bookid='.$version_id.'&q='.$query;
        return $api;
    }


}