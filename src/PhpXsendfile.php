<?php

namespace Mostafaznv\PhpXsendfile;

class PhpXsendfile
{
    /**
     * Config array
     *
     * @var array
     */
    protected $config = [];

    /**
     * Server Type
     *
     * @var null
     */
    protected $server = null;

    /**
     * User Defined Headers
     *
     * @var array
     */
    protected $extraHeaders = [];

    const CONFIG_PATH = __DIR__ . '/../config/config.php';

    const SERVER_APACHE    = 'Apache';
    const SERVER_NGINX     = 'Nginx';
    const SERVER_LIGHTTPD  = 'Lighttpd';
    const SERVER_LITESPEED = 'LiteSpeed';

    public function __construct(array $config = [])
    {
        $this->setConfig($config);
        $this->detectServer();
    }

    /**
     * Download Large Files
     *
     * @param string $file
     * @param string|null $fileName
     */
    public function download(string $file, string $fileName = null): void
    {
        $file = $this->absolutePath($file);
        $fileName = $fileName ?? basename($file);

        if ($this->config['cache']) {
            if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
                $modifiedSince = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
                $modifiedSince = strtotime($modifiedSince);

                if (filemtime($file) == $modifiedSince) {
                    header("HTTP/1.1 304: Not Modified");

                    return;
                }
            }

            if (isset($_SERVER['IF-NONE-MATCH']) and ($_SERVER['IF-NONE-MATCH'] == md5(filemtime($file)))) {
                header("HTTP/1.1 304: Not Modified");

                return;
            }
        }

        $this->setContentType($file);
        $this->setContentLength($file);
        $this->setContentDisposition($fileName);
        $this->setCacheHeaders($file);
        $this->setExtraHeaders();

        if ($this->server) {
            $uri = $this->pathToUri($file);

            switch ($this->server) {
                case self::SERVER_APACHE:
                    header("X-Sendfile: $uri");
                    break;

                case self::SERVER_NGINX:
                    header("X-Accel-Redirect: $uri");
                    break;

                case self::SERVER_LIGHTTPD:
                    header("X-LIGHTTPD-send-file: $uri");
                    break;

                case self::SERVER_LITESPEED:
                    header("X-LiteSpeed-Location: $uri");
                    break;
            }
        }
        else {
            // unknown server, use php stream

            ob_clean();
            flush();
            readfile($file);
        }

        exit();
    }

    public function setHeader(array $headers): PhpXsendfile
    {
        $this->extraHeaders = $headers;

        return $this;
    }

    /**
     * Initiate Config Array
     *
     * @param array $config
     */
    protected function setConfig(array $config): void
    {
        if (!empty($config)) {
            if (function_exists('config')) {
                $this->config = array_merge(config('x-sendfile'), $config);
            }
            else {
                $this->config = array_merge(include(self::CONFIG_PATH), $config);
            }
        }
        else {
            if (function_exists('config')) {
                $this->config = config('x-sendfile');
            }
            else {
                $this->config = include(self::CONFIG_PATH);
            }
        }
    }

    /**
     * Detect Server if server value in config file was null
     */
    protected function detectServer(): void
    {
        $servers = [
            'apache'    => self::SERVER_APACHE,
            'nginx'     => self::SERVER_NGINX,
            'lighttpd'  => self::SERVER_LIGHTTPD,
            'litespeed' => self::SERVER_LITESPEED,
        ];

        if ($this->config['server'] and in_array($this->config['server'], array_values($servers))) {
            $this->server = $this->config['server'];
        }
        else {
            $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? null;

            foreach ($servers as $name => $label) {
                if (stripos($serverSoftware, $name) !== false) {
                    $this->server = $label;

                    break;
                }
            }
        }
    }

    /**
     * Set User Defined Headers
     */
    protected function setExtraHeaders(): void
    {
        foreach ($this->extraHeaders as $header => $value) {
            header("$header: $value");
        }
    }

    /**
     * Set Content-type
     *
     * @param string $file
     */
    protected function setContentType(string $file): void
    {
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($fileInfo, $file);

        header('Content-type: ' . $mime ?? 'application/octet-stream');
    }

    /**
     * Set Content-Disposition
     *
     * @param string $fileName
     */
    protected function setContentDisposition(string $fileName): void
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $encodedFileName = rawurlencode($fileName);

        if (false !== strpos($userAgent, 'MSIE') or preg_match("/Trident\/7.0/", $userAgent)) {
            // ie
            header('Content-Disposition: attachment; filename="' . $encodedFileName . '"');
        }
        else if (false !== strpos($userAgent, "Firefox")) {
            // firefox
            header('Content-Disposition: attachment; filename*="utf8\'\'' . $encodedFileName . '"');
        }
        else {
            // safari and chrome
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
        }
    }

    /**
     * set Content-Length
     *
     * @param string $file
     */
    protected function setContentLength(string $file): void
    {
        header('Content-Length: ' . filesize($file));
    }

    /**
     * Set Last-Modified, Expires, Cache-Control, Etag
     *
     * @param string $file
     */
    protected function setCacheHeaders(string $file): void
    {
        if ($this->config['cache']) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT');
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $this->config['cache-control-max-age']) . ' GMT');
            header('Cache-Control: max-age=' . $this->config['cache-control-max-age']);
            header('Etag: " ' . md5(filemtime($file)) . '"');
        }
    }

    /**
     * Generate Absolute Path
     *
     * @param string $path
     * @return string
     */
    protected function absolutePath(string $path): string
    {
        return $this->basePath() . $this->pathToUri($path);
    }

    /**
     * Path to URI
     *
     * @param $path
     * @return string
     */
    protected function pathToUri($path): string
    {
        return '/' . ltrim(str_replace([$this->basePath(), '\\'], ['', '/'], $path), '/');
    }

    /**
     * Get Base Path of project
     *
     * @return string
     */
    protected function basePath(): string
    {
        if (isset($this->config['base-path']) and $this->config['base-path']) {
            return realpath($this->config['base-path']);
        }

        return $_SERVER['DOCUMENT_ROOT'];
    }
}
