<?php

namespace App\Service\Upload;

use Aws\S3\S3ClientInterface;
use Exception;

class UploadManager implements UploadInterface
{
    const YEAR_IN_SECONDS = 60 * 60 * 24 * 365;

    private S3ClientInterface $amazon;
    private string $bucket;

    public function __construct(S3ClientInterface $amazon, string $bucket)
    {
        $this->amazon = $amazon;
        $this->bucket = $bucket;
    }

    /**
     * @throws Exception
     */
    public function upload(string $visual, string $uuid): string
    {
        $outerCss = "'font-size: 10px; font-family: sans-serif;'";
        $innerCss = "'max-width:425px; padding: 30px; background: #fff8dc'";
        $htmlPrefix =
            "<!DOCTYPE html>
            <html lang=\"ru\">
            <head><meta charset=\"UTF-8\">
            <title>Receipt $uuid</title>
            <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
            </head>
            <body style=" . $outerCss . "><div style=" . $innerCss . ">";

        $visualByLines = explode("\r\n", $visual);
        foreach ($visualByLines as &$str) {
            $str = "<div>". trim($str) . "</div>";
        }

        $htmlSuffix = "</div></body></html>";
        $htmlInner = implode($visualByLines);
        $formattedReceiptStr = $htmlPrefix . $htmlInner . $htmlSuffix;

        $objectModel = $this->amazon->upload($this->bucket, $uuid, $formattedReceiptStr, 'private', [
            'params' => [
                'ContentType' => "text/html",
                'CacheControl' => 'public, max-age=' . self::YEAR_IN_SECONDS,
                'Expires' => 'Sun, 20 Dec 2023 21:31:12 GMT',
            ]
        ]);

        return $objectModel->get('ObjectURL');
    }
}
