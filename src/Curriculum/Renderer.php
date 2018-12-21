<?php
namespace Curriculum;

class Renderer
{
    /** @var PaneWriter[]  */
    private $panes = [];

    /** @var HeaderWriter */
    private $header;

    /** @var string */
    private $fileName = '';

    /**
     * @param PaneWriter[] $panes
     */
    public function __construct(HeaderWriter $header, array $panes, $fileName)
    {
        $this->setData($header, $panes);
        $this->fileName = $fileName;
    }

    /**
     * @param HeaderWriter $header
     * @param PaneWriter[] $panes
     */
    public function setData(HeaderWriter $header, array $panes)
    {
        $this->header = $header;

        foreach ($panes as $pane)
        {
            $this->addPane($pane);
        }
    }

    /**
     * @param PaneWriter $pane
     */
    public function addPane(PaneWriter $pane)
    {
        $this->panes[] = $pane;
    }

    public function render()
    {
        $this->renderHeader();

        $lines    = [];
        $maxCount = 0;
        foreach ($this->panes as $idx => $pane)
        {
            $lines[$idx]  = $pane->render();
            $paneRowCount = count($lines[$idx]);
            if ($paneRowCount > $maxCount)
            {
                $maxCount = $paneRowCount;
            }
        }

        for ($idx = 0 ; $idx < $maxCount ; $idx++)
        {
            foreach ($lines as $paneIdx => $paneLines)
            {
                $pane = $this->panes[$paneIdx];
                if (array_key_exists($idx, $paneLines))
                {
                    if (($pane->getTotalLength() - mb_strlen($paneLines[$idx])) + 1 < 0) {
                        var_dump($pane->getTotalLength(), mb_strlen($paneLines[$idx]));
                        var_dump($paneLines[$idx]);
                        die();
                    }
                    echo $paneLines[$idx].str_repeat(' ', ($pane->getTotalLength() - mb_strlen($paneLines[$idx])) + 1);
                }
                else
                {
                    echo str_repeat(' ', $pane->getTotalLength() + 1);
                }
            }
            echo PHP_EOL;
        }
    }

    /**
     * @return string
     */
    public function renderAndReturn()
    {
        ob_start();
        $this->render();
        $str = ob_get_contents();
        ob_end_clean();

        return $str;
    }

    public function renderAndSave()
    {
        file_put_contents('generated/'.$this->fileName, $this->renderAndReturn());
    }

    private function renderHeader()
    {
        foreach ($this->panes as $pane)
        {
            $this->header->addPane($pane);
        }

        $this->header->calculateTotalLength();

        foreach ($this->header->render() as $line)
        {
            echo $line.PHP_EOL;
        }
    }
}
