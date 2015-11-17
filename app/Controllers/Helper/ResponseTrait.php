<?php

// app/Controllers/Helper/ResponseTrait.php

namespace Controllers\Helper;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Trait - ResponseTrait
 * response operations
 *
 * @category Helper
 * @package  app\Controllers\Helper
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
trait ResponseTrait
{
    
    public $mimeTypes = array(
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',
        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',
        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',
        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',
        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    );
    
    /**
     * Transmit data in XML format
     *
     * @param string $aXml The response data
     * @param int $status The response status code
     * @return string 
     */
    protected function sendXml($aXml, $status = 200) {
        return new Response($aXml, $status, array('Content-type' => 'text/xml; charset=utf-8'));
    }
    
    /**
     * Transmit data in Json format
     *
     * @param mixed $aData The response data
     * @param int $status The response status code
     * @param array $headers An array of response headers
     * @return JsonResponse 
     */
    protected function sendJson($aData, $status = 200, array $headers = array()) {
        return $this->app->json($aData, $status, $headers);
    }

    /**
     * Send compress data
     *
     * @param string $aData The response data
     * @param int $status The response status code
     * @return string 
     */
    protected function sendGzip($aData, $status = 200) {
        return new Response($aData, $status, array(
            'Content-type' => 'text/html; charset=utf-8',
            'Content-Encoding' => 'gzip'
        ));
    }
    
    /**
     * Stream of file
     *
     * @param string $file      The file path
     * @param bool   $sendFile  Use sendFile or stream
     * @param int $status The response status code
     *
     * @return StreamedResponse A StreamedResponse instance
     */
    public function sendFile($file, $sendFile = TRUE, $status = 200) {
        $mimeTypes = $this->mimeTypes;
        //-------------------------
        if (!file_exists($file)) {
            return $this->app->abort(404, "The file \"{$file}\" was not found.");
        }

        // Get extension
        $path_parts = pathinfo($file);
        $ext = $path_parts['extension'];
        if (!$ext) {
            return $this->app->abort(404, "There is no file extension");
        }
        if (!isset($mimeTypes[$ext])) {
            return $this->app->abort(404, "There is no mime type for such \".{$ext}\" a file extension");
        }
        $mimeType = $mimeTypes[$ext];

        $stream = function () use ($file) {
            readfile($file);
        };

        if ($sendFile) {
            return $this->app->sendFile($file, $status, array('Content-Type' => $mimeType));
        } else {
            return $this->app->stream($stream, $status, array('Content-Type' => $mimeType));
        }
    }
    
    /**
     * Returns a NotFoundHttpException.
     *
     * This will result in a 404 response code. Usage example:
     *
     *     throw $this->createNotFoundException('Page not found!');
     *
     * @param string          $message  A message
     * @param \Exception|null $previous The previous exception
     *
     * @return NotFoundHttpException
     */
    public function createNotFoundException($message = 'Not Found', \Exception $previous = null) {
        return new NotFoundHttpException($message, $previous);
    }
}
