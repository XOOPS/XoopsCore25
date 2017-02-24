<?php

class PatchStatus
{
    /** @var string $patchClass class name of patch */
    public $patchClass;

    /** @var bool $applied true if this patch is applied, false if it is needed */
    public $applied = true;

    /** @var string[] $tasks tasks that need to be run */
    public $tasks = array();

    /** @var string[] $files files that need to be writable */
    public $files = array();

    /**
     * PatchStatus constructor.
     * @param XoopsUpgrade $patch
     */
    public function __construct(XoopsUpgrade $patch)
    {
        $this->patchClass = get_class($patch);
        foreach ($patch->tasks as $task) {
            if (!$patch->{"check_{$task}"}()) {
                $this->addTask($task);
            }
        }
        if (!empty($patch->usedFiles) && !$this->applied) {
            $this->files = $patch->usedFiles;
        }
    }

    /**
     * Add a task that needs to be run to the tasks property
     *
     * @param string $task task name
     */
    protected function addTask($task)
    {
        $this->tasks[] = $task;
        $this->applied = false;
    }
}
