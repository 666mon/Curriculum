<?php
namespace Library;

class MB
{
    /**
     * @param $input
     * @param $pad_len
     * @param string $pad_str
     * @param int $pad_type
     * @param string $encoding
     * @return string
     */
    public static function str_pad($input, $pad_len, $pad_str = ' ', $pad_type = STR_PAD_RIGHT, $encoding = 'UTF-8')
    {
        $padLength = strlen($input) - mb_strlen($input, $encoding) + $pad_len;
        return str_pad($input, $padLength, $pad_str, $pad_type);
    }

    /**
     * @param $str
     * @param int $width
     * @param string $break
     * @param bool $cut
     * @return string
     */
    public static function wordwrap($str, $width = 75, $break = "\n", $cut = false)
    {
        $lines = explode($break, $str);
        foreach ($lines as &$line)
        {
            $line = rtrim($line);
            if (mb_strlen($line) <= $width)
            {
                continue;
            }
            $words  = explode(' ', $line);
            $line   = '';
            $actual = '';
            foreach ($words as $word)
            {
                if (mb_strlen($actual.$word) <= $width)
                {
                    $actual .= $word.' ';
                }
                else
                {
                    if ($actual != '')
                    {
                        $line .= rtrim($actual).$break;
                    }
                    $actual = $word;
                    if ($cut)
                    {
                        while (mb_strlen($actual) > $width)
                        {
                            $line  .= mb_substr($actual, 0, $width).$break;
                            $actual = mb_substr($actual, $width);
                        }
                    }
                    $actual .= ' ';
                }
            }
            $line .= trim($actual);
        }
        return implode($break, $lines);
    }
}
