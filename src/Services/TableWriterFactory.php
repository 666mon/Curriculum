<?php
namespace Services;

use Curriculum\TableWriter;

class TableWriterFactory
{
    /**
     * @param array $data
     *
     * @return TableWriter
     */
    public function create(array $data)
    {
        return new TableWriter($data);
    }
}
