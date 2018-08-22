<?php
namespace BigFileUpload\Laravel\ChunkUpload\Handler;

/**
 * Class ChunksInRequestSimpleUploadHandler
 *
 * Upload receiver that detects the content range from he request value - chunks
 * Works with:
 * - simple-uploader: https://github.com/simple-uploader
 *
 * @package Pion\Laravel\ChunkUpload\Handler
 */
class ChunksInRequestSimpleUploadHandler extends ChunksInRequestUploadHandler
{
    /**
     * Key for number of sending chunk
     * @static string
     */
    const KEY_CHUNK_NUMBER = 'chunkNumber';

    /**
     * Key for number of all chunks
     * @static string
     */
    const KEY_ALL_CHUNKS = 'totalChunks';
}
