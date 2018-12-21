<?php
namespace Curriculum;

class PaneWriter extends AbstractWriter
{
    /** @var TableWriter[] */
    private $tables = [];

    /**
     * @param TableWriter $table
     */
    public function addTable(TableWriter $table)
    {
        $this->tables[] = $table;
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        foreach ($data['tables'] as $tableData)
        {
            $this->addTable(TableWriter::create($tableData));
        }

        $this->calculateTotalLength();
    }

    public function calculateTotalLength()
    {
        $this->totalLength = 0;

        foreach ($this->tables as $table)
        {
            $totalLength = $table->getTotalLength();
            if ($this->totalLength < $totalLength)
            {
                $this->totalLength = $totalLength;
            }
        }
    }

    /**
     * @return array
     */
    public function render()
    {
        $lines = [];

        foreach ($this->tables as $table)
        {
            foreach ($table->render() as $line)
            {
                $lines[] = $line;
            }
            $lines[] = str_repeat(' ', $table->getTotalLength());
        }

        return $lines;
    }
}
