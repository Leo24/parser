<?php

namespace MyApp\PageParser;

class PageParser
{
    /**
     * @var string the url to get images from
     */
    public $url;
    public $domain;
    public $directory;

    const FILE_DIRECTORY = '/PageParser/files/';
    const FILE_EXTENSION = '.csv';


    public function __construct()
    {
        $this->directory = dirname(dirname(__FILE__));
    }

    /**
     * Get images from url and put them to directory
     */
    public function parse($url)
    {
        $this->url = $this->addHttp($url);
        $this->domain = $this->getDomain($this->url);

        $links = [];
        // Create DOM from URL or file
        $initialPage = file_get_contents($this->url);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML($initialPage);
        $links[] = $this->url;

        foreach($dom->getElementsByTagName('a') as $link) {
            if (strpos($link->getAttribute('href'), $this->domain)) {
                $links[] = $link->getAttribute('href');
            }
        }

        $file = $this->getImages($links);

        echo 'You can find image links in \''.$file.'\' file';



    }

    public function report($url) {
        $this->url = $this->addHttp($url);
        $this->domain = $this->getDomain($this->url);
        $file = $this->directory . self::FILE_DIRECTORY . basename($this->domain) . self::FILE_EXTENSION;
        $row = 1;
        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                echo "$num images found for $this->domain : \n";
                $row++;
                for ($c=0; $c < $num; $c++) {
                    echo $data[$c] . "\n";
                }
            }
            fclose($handle);
        }

    }

    public function help() {

    }

    /**
     * @param $url
     * @return string
     */
    private function addHttp($url) {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }
        return $url;
    }

    /**
     * @param $url
     * @return bool
     */
    private function getDomain($url) {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }
        return false;
    }

    private function getImages($links){
        $file = $this->directory . self::FILE_DIRECTORY . basename($this->domain) . self::FILE_EXTENSION;
        foreach($links as $link){
            $images = [];
            $html = file_get_contents($link);
            $dom = new \DOMDocument();
            $dom->loadHTML($html);
            foreach ($dom->getElementsByTagName('img') as $image) {
                $images[] = $image->getAttribute('src');
            }
            $fp = fopen($file, 'w') or die ("Unable to open file!");
            fputcsv($fp, $images);
            fclose($fp);
        }
        if (file_exists($file)){
            return $file;
        }

    }

}

