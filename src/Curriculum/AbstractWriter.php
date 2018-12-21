<?php
namespace Curriculum;

abstract class AbstractWriter
{
    /** @var int */
    protected $totalLength = 0;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->setData($data);
    }

    abstract function calculateTotalLength();

    /**
     * @param array $data
     */
    abstract function setData(array $data);

    /**
     * @return int
     */
    public function getTotalLength()
    {
        return $this->totalLength;
    }
}
