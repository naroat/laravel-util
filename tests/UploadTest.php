<?php

use PHPUnit\Framework\TestCase;
class UploadTest extends TestCase
{
    /** @test */
    public function it_can_upload_image()
    {
        $img = \Illuminate\Http\UploadedFile::fake()->image('image.jpg');

        $response = $this->json('POST', '/fileupload', ['file' => $img]);

        \Illuminate\Support\Facades\Storage::disk('public')->assertExists('images/test.jpg');
        //$this->assertTrue(true);
    }
}