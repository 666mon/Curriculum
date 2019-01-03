<?php
namespace Curriculum\TableWriter;

use Curriculum\TableWriter;

class Factory
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
