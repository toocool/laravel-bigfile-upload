<?php

namespace ChunkTests\Handler;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\TestCase;
use BigFileUpload\Laravel\ChunkUpload\Config\FileConfig;
use BigFileUpload\Laravel\ChunkUpload\Handler\NgFileUploadHandler;

class NgFileUploadHandlerTest extends TestCase
{

    /**
     * @throws \Exception
     */
    public function testInitWithoutChunk()
    {
        $request = Request::create('test', 'POST', [], [], [], []);
        $file = UploadedFile::fake();

        /** @var UploadedFile $file */
        $ngFileUpload = new NgFileUploadHandler($request, $file, new FileConfig());

        $this->assertFalse($ngFileUpload->isChunkedUpload());
    }

    /**
     * @throws \Exception
     */
    public function testInitWithChunk()
    {
        $request = Request::create(
            'test',
            'POST',
            [
                '_chunkNumber'      => 10,
                '_totalSize'        => 5000,
                '_chunkSize'        => 500,
                '_currentChunkSize' => 500,
            ]
        );
        $file = UploadedFile::fake();

        /** @var UploadedFile $file */
        $ngFileUpload = new NgFileUploadHandler($request, $file, new FileConfig());

        $this->assertTrue($ngFileUpload->isChunkedUpload());
    }

    /**
     * @throws \Exception
     */
    public function testPercentageDoneWithoutChunk()
    {
        $request = Request::create('test', 'POST', [], [], [], []);
        $file = UploadedFile::fake();

        /** @var UploadedFile $file */
        $ngFileUpload = new NgFileUploadHandler($request, $file, new FileConfig());

        $this->assertEquals(0, $ngFileUpload->getPercentageDone());
    }

    /**
     * @throws \Exception
     */
    public function testValidNgFileUploadFirstChunk()
    {
        $request = Request::create(
            'test',
            'POST',
            [
                '_chunkNumber'      => 0,
                '_totalSize'        => 5000,
                '_chunkSize'        => 500,
                '_currentChunkSize' => 500,
            ]
        );
        $file = UploadedFile::fake()->create('test');

        $ngFileUpload = new NgFileUploadHandler($request, $file, new FileConfig());

        $this->assertEquals(1, $ngFileUpload->getCurrentChunk());
        $this->assertEquals(10, $ngFileUpload->getTotalChunks());
        $this->assertEquals(10, $ngFileUpload->getPercentageDone());
        $this->assertTrue($ngFileUpload->isChunkedUpload());
        $this->assertFalse($ngFileUpload->isLastChunk());
        $this->assertTrue($ngFileUpload->isFirstChunk());
    }

    /**
     * @throws \Exception
     */
    public function testValidNgFileUploadNextChunk()
    {
        $request = Request::create(
            'test',
            'POST',
            [
                '_chunkNumber'      => 1,
                '_totalSize'        => 5000,
                '_chunkSize'        => 500,
                '_currentChunkSize' => 500,
            ]
        );
        $file = UploadedFile::fake();

        /** @var UploadedFile $file */
        $ngFileUpload = new NgFileUploadHandler($request, $file, new FileConfig());

        $this->assertEquals(2, $ngFileUpload->getCurrentChunk());
        $this->assertEquals(10, $ngFileUpload->getTotalChunks());
        $this->assertEquals(20, $ngFileUpload->getPercentageDone());
        $this->assertTrue($ngFileUpload->isChunkedUpload());
        $this->assertFalse($ngFileUpload->isLastChunk());
        $this->assertFalse($ngFileUpload->isFirstChunk());
    }

    /**
     * @throws \Exception
     */
    public function testIsLastChunk()
    {
        $request = Request::create(
            'test',
            'POST',
            [
                '_chunkNumber'      => 9,
                '_totalSize'        => 5000,
                '_chunkSize'        => 500,
                '_currentChunkSize' => 500,
            ]
        );
        $file = UploadedFile::fake();

        /** @var UploadedFile $file */
        $ngFileUpload = new NgFileUploadHandler($request, $file, new FileConfig());

        $this->assertEquals(10, $ngFileUpload->getCurrentChunk());
        $this->assertEquals(10, $ngFileUpload->getTotalChunks());
        $this->assertEquals(100, $ngFileUpload->getPercentageDone());
        $this->assertTrue($ngFileUpload->isLastChunk());
        $this->assertFalse($ngFileUpload->isFirstChunk());
    }

    /**
     * Checks if canBeUsedForRequest returns false when chunk is missing
     *
     * @throws \Exception
     */
    public function testCanBeUsedForInvalidRequest()
    {
        $request = Request::create('test', 'POST', [], [], [], []);
        $this->assertFalse(NgFileUploadHandler::canBeUsedForRequest($request));
    }

    /**
     * Checks if canBeUsedForRequest returns false when content-range is invalid
     *
     * @throws \Exception
     */
    public function testCanBeUsedForInvalidContentRangeFormat()
    {
        $request = Request::create(
            'test',
            'POST',
            [
                '_chunkNumber'      => 'xx',
                '_totalSize'        => 'xx',
                '_chunkSize'        => 'xx',
                '_currentChunkSize' => 'xx',
            ]
        );
        $this->assertFalse(NgFileUploadHandler::canBeUsedForRequest($request));
    }

    /**
     * Checks if canBeUsedForRequest returns false when content-range is missing
     *
     * @throws \Exception
     */
    public function testCanBeUsedForValidRange()
    {
        $request = Request::create(
            'test',
            'POST',
            [
                '_chunkNumber'      => '0',
                '_totalSize'        => '10',
                '_chunkSize'        => '10',
                '_currentChunkSize' => '10',
            ]
        );
        $this->assertTrue(NgFileUploadHandler::canBeUsedForRequest($request));
    }
}
