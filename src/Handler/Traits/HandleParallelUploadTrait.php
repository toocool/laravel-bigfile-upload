<?php

namespace BigFileUpload\Laravel\ChunkUpload\Handler\Traits;

use BigFileUpload\Laravel\ChunkUpload\Exceptions\ChunkSaveException;
use BigFileUpload\Laravel\ChunkUpload\Save\ParallelSave;
use BigFileUpload\Laravel\ChunkUpload\Storage\ChunkStorage;

trait HandleParallelUploadTrait
{
    protected $percentageDone = 0;

    /**
     * Returns the chunk save instance for saving
     *
     * @param ChunkStorage    $chunkStorage the chunk storage
     *
     * @return ParallelSave
     * @throws ChunkSaveException
     */
    public function startSaving($chunkStorage)
    {
        // Build the parallel save
        return new ParallelSave(
            $this->file,
            $this,
            $chunkStorage,
            $this->config
        );
    }
}