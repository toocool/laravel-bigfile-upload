<?php
namespace BigFileUpload\Laravel\ChunkUpload\Handler;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use BigFileUpload\Laravel\ChunkUpload\Config\AbstractConfig;
use BigFileUpload\Laravel\ChunkUpload\Handler\Traits\HandleParallelUploadTrait;

class DropZoneUploadHandler extends ChunksInRequestUploadHandler
{
    use HandleParallelUploadTrait;

    const CHUNK_UUID_INDEX = 'dzUuid';
    const CHUNK_INDEX = 'dzChunkIndex';
    const CHUNK_FILE_SIZE_INDEX = 'dzTotalFileSize';
    const CHUNK_SIZE_INDEX = 'dzChunkSize';
    const CHUNK_TOTAL_INDEX = 'dzTotalChunkCount';
    const CHUNK_OFFSET_INDEX = 'dzChunkByteOffset';

    /**
     * The DropZone file uuid
     * @var string|null
     */
    protected $fileUuid = null;

    /**
     * AbstractReceiver constructor.
     *
     * @param Request        $request
     * @param UploadedFile   $file
     * @param AbstractConfig $config
     */
    public function __construct(Request $request, $file, $config)
    {
        parent::__construct($request, $file, $config);
        $this->fileUuid = $request->get(self::CHUNK_UUID_INDEX);
    }


    /**
     * Builds the chunk file name from file uuid and current chunk
     * @return string
     */
    public function getChunkFileName()
    {
        return $this->createChunkFileName($this->fileUuid, $this->getCurrentChunk());
    }

    /**
     * Returns current chunk from the request
     *
     * @param Request $request
     *
     * @return int
     */
    protected function getCurrentChunkFromRequest(Request $request)
    {
        return intval($request->get(self::CHUNK_INDEX, 0)) + 1;
    }

    /**
     * Returns current chunk from the request
     *
     * @param Request $request
     *
     * @return int
     */
    protected function getTotalChunksFromRequest(Request $request)
    {
        return intval($request->get(self::CHUNK_TOTAL_INDEX, 1));
    }


    /**
     * Checks if the current abstract handler can be used via HandlerFactory
     *
     * @param Request $request
     *
     * @return bool
     */
    public static function canBeUsedForRequest(Request $request)
    {
        return $request->has(self::CHUNK_UUID_INDEX) && $request->has(self::CHUNK_TOTAL_INDEX) &&
            $request->has(self::CHUNK_INDEX);
    }
}
