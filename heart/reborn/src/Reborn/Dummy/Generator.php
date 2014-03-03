<?php

namespace Reborn\Dummy;

/**
 * Dummy Text Generator for Theme Development
 *
 * @package Reborn\Dummy
 * @author Myanmar Links Professional Web Development
 **/
class Generator extends \Facade
{

    /**
     * Dummy text file
     *
     * @var string
     */
    protected $file_path;

    /**
     * Constructor method
     */
    public function __construct()
    {
        $this->file_path = __DIR__.DS.'stub'.DS;
    }

    /**
     * Generate paragraph
     *
     * @param  int      $amount     Paragraph amount
     * @param  int|null $word_limit Paragraph word limit
     * @param  string   $ending     Word limit ending. eg(...)
     * @return text
     */
    public function generateParagraph($amount = 1, $word_limit = null, $ending = '')
    {
        $texts = $this->getText($amount);

        $no_limit = is_null($word_limit);

        foreach ($texts as $t) {
            if ($no_limit) {
                $result[] = '<p>'.$t.'</p>';
            } else {
                $result[] = '<p>'.\Str::words($t, $word_limit, $ending).'</p>';
            }
        }

        return implode("\n", $result);
    }

    /**
     * Generate li
     *
     * @param  int     $amount      li tag amount
     * @param  boolean $with_anchor Use li with anchor tag
     * @return string
     **/
    public function generateLi($amount = 5, $with_anchor = false)
    {
        $texts = $this->getText($amount, 'list');

        foreach ($texts as $t) {
            if ($with_anchor) {
                $result[] = '<li><a href="#">'.$t.'</a></li>';
            } else {
                $result[] = '<li>'.$t.'</li>';
            }
        }

        return implode("\n", $result);
    }

    /**
     * Generate Table with 4 columns 4 rows
     *
     * @param  string $class Class for table
     * @param  string $id    Id for table
     * @return string
     **/
    public function generateTable($class = '', $id = '')
    {
        if (\File::is($this->file_path.'en.php')) {
            $words = require $this->file_path.'en.php';
        }

        $headers = $words['table']['header'];
        $body = $words['table']['body'];

        $result = '<table class="'.$class.'" id="'.$id.'" >';
        $result .= '<thead><tr>';

        foreach ($headers as $head) {
            $result .= '<th>'.$head.'</th>';
        }

        $result .= '</tr><tbody>';

        foreach ($body as $k => $v) {
            $result .= '<tr>';
            foreach ($v as $l) {
                $result .= '<td>'.$l.'</td>';
            }
        }
        $result .= '</tbody></table>';

        return $result;
    }

    /**
     * Get Blog Post Body
     *
     * @return string
     **/
    public function generatePost()
    {
        if (\File::is($this->file_path.'post.txt')) {
            return \File::getContent($this->file_path.'post.txt');
        }

        return null;
    }

    /**
     * Get dummy text data from dummy text file.
     *
     * @param  int    $amount
     * @param  string $type   Text type
     * @return array
     **/
    protected function getText($amount = 1, $type = 'p')
    {
        if (\File::is($this->file_path.'en.php')) {
            $words = require $this->file_path.'en.php';
        }

        $words = $words[$type];

        for ($i=0; $i < (int) $amount; $i++) {
            $key = $i % count($words);
            $result[] = $words[$key];
        }

        return $result;
    }

    /**
     * Get Instance method for Facade
     *
     * @return \Reborn\Dummy\Generator
     **/
    protected static function getInstance()
    {
        return new static();
    }

    /**
     * Solve static call for object
     *
     * @param  string $method
     * @param  array  $args
     * @return void
     **/
    public static function __callStatic($method, $args)
    {
        $method = 'generate'.ucfirst($method);

        return parent::__callStatic($method, $args);
    }

} // END class Generator
