<?php
namespace Curriculum;

use Library\MB;

class TableWriter extends AbstractWriter
{
    const PADDING = ' ';

    const PAD_LENGTH = 1;

    /** @var array */
    private $columns = [];

    /** @var array */
    private $rows = [];

    /** @var bool */
    private $rowSeparator = false;

    /** @var string */
    private $separator = '';

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        foreach ($data['columns'] as $idx => $column)
        {
            $this->columns[$idx] = array_merge($column, ['idx' => $idx]);
        }

        $this->rows = $data['rows'];
        if (array_key_exists('row_separator', $data))
        {
            if (!is_bool($data['row_separator']))
            {
                throw new \UnexpectedValueException('row_separator must be a boolean.');
            }
            $this->rowSeparator = $data['row_separator'];
        }

        $this->calculateTotalLength();
        $this->calculateSeparator();
    }

    /**
     * @return array
     */
    public function render()
    {
        $lines = $this->getHeaderLines();

        // Calculate lines appearance.
        $rowCount = 0;
        foreach ($this->rows as $row)
        {
            $rowIsComplete = true;
            $chunks  = $this->splitRow($row);
            $lineSet = [];
            foreach ($chunks as $splitRow)
            {
                $line = '';
                foreach ($this->columns as $idx => $column)
                {
                    if (array_key_exists($idx, $splitRow))
                    {
                        $line .= '|'.self::PADDING.MB::str_pad($splitRow[$idx], $column['length'] - self::PAD_LENGTH);
                    }
                    else
                    {
                        $rowIsComplete = false;
                        $line .= MB::str_pad(self::PADDING, $column['length'] + self::PAD_LENGTH);
                    }
                }
                $line .= '|';
                $lineSet[] = $line;
            }
            if (!$rowIsComplete && $rowCount > 0)
            {
                $lines[] = $this->separator;
                $rowCount++;
            }
            $rowCount += count($chunks);
            $lines = array_merge($lines, $lineSet);
            if (!$rowIsComplete || $this->rowSeparator)
            {
                $lines[] = $this->separator;
                $rowCount++;
            }
        }

        // Add last separator for table bottom.
        if (!$this->rowSeparator)
        {
            $lines[] = $this->separator;
        }

        return $lines;
    }

    public function calculateTotalLength()
    {
        $this->totalLength = self::PAD_LENGTH;

        foreach ($this->columns as $column)
        {
            $this->totalLength += $column['length'] + self::PAD_LENGTH;
        }
    }

    private function calculateSeparator()
    {
        $this->separator = '+';

        foreach ($this->columns as $column)
        {
            $this->separator .= str_repeat('-', $column['length'] + (self::PAD_LENGTH - 1)) . '+';
        }
    }

    /**
     * @param array $row
     * @return array
     */
    private function splitRow(array $row)
    {
        $chunkTable = [];

        foreach ($this->columns as $idx => $column)
        {
            if (array_key_exists($idx, $row))
            {
                $body = $row[$idx];
                if (array_key_exists('paragraph', $column))
                {
                    $body = $column['paragraph'].$body;
                }
                $chunks = explode("\n", MB::wordwrap($body, $column['length'] - (self::PAD_LENGTH + 1), "\n"));
                foreach ($chunks as $chunkIdx => $chunk)
                {
                    $chunkTable[$chunkIdx][$idx] = $chunk;
                }
            }
        }

        foreach ($chunkTable as $lineIdx => &$line)
        {
            foreach ($this->columns as $idx => $column)
            {
                if (array_key_exists($idx, $row) && !array_key_exists($idx, $line))
                {
                    $line[$idx] = '';
                }
            }
        }

        return $chunkTable;
    }

    /**
     * @return array
     */
    private function getHeaderLines()
    {
        // Calculate table header.
        $headerLine = '';

        foreach ($this->columns as $column)
        {
            $headerLine .= '|'.
                self::PADDING.
                MB::str_pad($column['title'], $column['length'] - self::PAD_LENGTH);
        }
        $headerLine .= '|';

        return [$this->separator, $headerLine, $this->separator];
    }
}
