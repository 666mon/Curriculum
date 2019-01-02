<?php
namespace Curriculum;

use Library\MB;

class HeaderWriter extends AbstractWriter
{
    /** @var array */
    private $header = [];

    /** @var PaneWriter[] */
    private $panes = [];

    /** @var string */
    private $jobTitle = '';

    /** @var array  */
    private $picture = [];

    /** @var int */
    private $pictureLength = 0;

    /**
     * @param array  $data
     * @param string $jobTitle
     * @param array  $picture
     */
    public function __construct(array $data, $jobTitle, array $picture = [])
    {
        parent::__construct($data);

        // Add and format Job Title.
        $this->jobTitle = '### '.$jobTitle.' ###';

        // Add blank lines at top and bottom of ascii picture.
        $this->picture  = array_merge([''], $picture, ['']);

        $this->calculatePictureLength();
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->header = $data;
    }

    /**
     * @param PaneWriter $pane
     */
    public function addPane(PaneWriter $pane)
    {
        $this->panes[] = $pane;
    }

    public function calculateTotalLength()
    {
        foreach ($this->panes as $pane)
        {
            $this->totalLength += $pane->getTotalLength();
        }
    }

    /**
     * @return array
     */
    public function render()
    {
        $lines   = [];
        $length  = $this->getTotalLength() - 1;
        $lines[] = $this->generateBorder();

        $maxCount = max(count($this->header), count($this->picture));

        for ($idx = 0 ; $idx < $maxCount ; $idx++)
        {
            $line = '|';
            if ($this->pictureLength > 0 && array_key_exists($idx, $this->picture))
            {
                $line .= MB::str_pad($this->picture[$idx], $this->pictureLength).'|';
            }
            $picLengthWithPad = ($this->pictureLength > 0)? $this->pictureLength + 1 : $this->pictureLength;
            if (array_key_exists($idx, $this->header))
            {
                $padLength = $length - TableWriter::PAD_LENGTH - $picLengthWithPad;
                $line      .= TableWriter::PADDING.MB::str_pad($this->header[$idx], $padLength).'|';
            }
            else
            {
                $line .= TableWriter::PADDING.str_repeat(' ', $length - $picLengthWithPad - 1).'|';
            }

            $lines[] = $line;
        }

        $lines[] = $this->generateBorder();

        // Write job title with top and bottom margin.
        $lines[]     = '';
        $titleLength = mb_strlen($this->jobTitle);
        $clear       = ceil(($this->getTotalLength() - $titleLength) / 2);
        $lines[]     = str_repeat(' ', $clear).$this->jobTitle;
        $lines[]     = '';

        return $lines;
    }

    private function calculatePictureLength()
    {
        foreach ($this->picture as $line)
        {
            $lineLength = mb_strlen($line);
            if ($lineLength > $this->pictureLength)
            {
                $this->pictureLength = $lineLength;
            }
        }
    }

    /**
     * @return string
     */
    private function generateBorder()
    {
        if ($this->pictureLength > 0)
        {
            return '+'.
                str_repeat('-', $this->pictureLength).
                '+'.
                str_repeat('-', $this->getTotalLength() - $this->pictureLength - 2).
                '+';
        }
        else
        {
            return '+'.str_repeat('-', $this->getTotalLength() - 1).'+';
        }
    }
}
