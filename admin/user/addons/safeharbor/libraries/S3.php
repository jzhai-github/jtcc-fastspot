<?php
/**
 * Amazon S3 API Wrapper
 *
 * @author      JR Tashjian <jr@packettide.com>
 * @copyright   Copyright (c) 2011, Packet Tide, LLC
 * @link        http://packettide.com
 */
class S3 extends Request
{
    private $endpoint;

    private $access_key;
    private $secret_access_key;

    private $date;

    public function __construct($access_key, $secret_access_key, $endpoint='s3.amazonaws.com')
    {
        if (substr(strtolower($endpoint), 0, 4) == 'http') {
            $endpoint = substr($endpoint, strpos($endpoint, '//')+2);
        }

        $this->endpoint = $endpoint;

        $this->access_key = $access_key;
        $this->secret_access_key = $secret_access_key;

        $this->date = gmdate('r');
    }

    public function get_endpoint()
    {
        return $this->endpoint;
    }

    public function list_buckets()
    {
        $request = array(
            'method' => 'GET',
            'resource' => '/',
        );

        return $this->send_request($request);
    }

    public function bucket_exists($bucket)
    {
        $request = array(
            'method' => 'GET',
            'resource' => "/$bucket/",
        );

        $bucket_response = $this->send_request($request);

        if (!empty($bucket_response) && !empty($bucket_response->Name)) {
            return true;
        } else {
            return false;
        }
    }

    public function create_bucket($bucket)
    {
        $request = array(
            'method' => 'PUT',
            'resource' => "/$bucket/",
        );

        $this->set_header('Content-Length', '0');
        $this->set_header('x-amz-acl', 'authenticated-read');

        return $this->send_request($request);
    }

    public function delete_bucket($bucket)
    {
        $request = array(
            'method' => 'DELETE',
            'resource' => "/$bucket/",
        );

        return $this->send_request($request);
    }

    public function create_object($bucket, $remote_path, $local_path)
    {
        $request = array(
            'method' => 'PUT',
            'resource' => "/$bucket/$remote_path",
        );

        $file = fopen($local_path, 'r');

        $this->set_option('CURLOPT_PUT', true);
        $this->set_option('CURLOPT_INFILE', $file);
        $this->set_option('CURLOPT_INFILESIZE', filesize($local_path));

        if (empty($this->headers['Content-Type'])) {
            $ext = strtolower(pathinfo($local_path, PATHINFO_EXTENSION));
            $this->set_header('Content-Type', isset($this->mime_types[$ext]) ? $this->mime_types[$ext] : 'application/octet-stream');
        }
        $request['content-type'] = $this->headers['Content-Type'];

        $result = $this->send_request($request);
        fclose($file);

        return $result;
    }

    public function delete_object($bucket, $remote_path)
    {
        $request = array(
            'method' => 'DELETE',
            'resource' => "/$bucket/$remote_path",
        );

        return $this->send_request($request);
    }

    public function get_object_url($bucket, $remote_path)
    {
        $request = array(
            'method' => 'GET',
            'resource' => "/$bucket/$remote_path",
            'date' => time() + 3600,
        );

        $authentication_params  = '?AWSAccessKeyId=' . $this->access_key;
        $authentication_params .= '&Expires=' . $request['date'];
        $authentication_params .= '&Signature=' . urlencode($this->signature($request));

        return 'http://s3.amazonaws.com' . $request['resource'] . $authentication_params;
    }

    private function send_request($request)
    {
        $this->set_header('Date', $this->date);
        $this->set_header('Authorization', 'AWS ' . $this->access_key . ':' . $this->signature($request));

        $url = 'http://' . $this->endpoint . $request['resource'];

        switch ($request['method']) {
            case 'GET':
                $data = $this->get($url);
                break;
            case 'POST':
                $data = $this->post($url);
                break;
            case 'PUT':
                $data = $this->put($url);
                break;
            case 'DELETE':
                $data = $this->delete($url);
                break;
        }

        return simplexml_load_string($data);
    }

    private function signature($request)
    {
        $canonicalized_amz_headers_array = array();
        $canonicalized_amz_headers_string = '';
        foreach ($this->headers as $key => $value) {
            $key = strtolower($key);

            if (substr($key, 0, 5) != 'x-amz') {
                continue;
            }

            if (isset($canonicalized_amz_headers_array[$key])) {
                $canonicalized_amz_headers_array[$key] .= ',' . trim($value);
            } else {
                $canonicalized_amz_headers_array[$key] = trim($value);
            }
        }
        ksort($canonicalized_amz_headers_array);

        foreach ($canonicalized_amz_headers_array as $key => $value) {
            $canonicalized_amz_headers_string .= $key . ':' . $value . "\n";
        }

        $string  = $request['method'] . "\n";
        $string .= empty($request['content-md5']) ? "\n" : $request['content-md5'] . "\n";
        $string .= empty($request['content-type']) ? "\n" : $request['content-type'] . "\n";
        $string .= empty($request['date']) ? $this->date . "\n" : $request['date'] . "\n";
        $string .= $canonicalized_amz_headers_string . $request['resource'];
        $string = utf8_encode($string);

        $hash = hash_hmac('sha1', $string, $this->secret_access_key, true);
        return base64_encode($hash);
    }

    private $mime_types = array("323" => "text/h323", "acx" => "application/internet-property-stream", "ai" => "application/postscript", "aif" => "audio/x-aiff", "aifc" => "audio/x-aiff", "aiff" => "audio/x-aiff",
            "asf" => "video/x-ms-asf", "asr" => "video/x-ms-asf", "asx" => "video/x-ms-asf", "au" => "audio/basic", "avi" => "video/quicktime", "axs" => "application/olescript", "bas" => "text/plain", "bcpio" => "application/x-bcpio", "bin" => "application/octet-stream", "bmp" => "image/bmp",
            "c" => "text/plain", "cat" => "application/vnd.ms-pkiseccat", "cdf" => "application/x-cdf", "cer" => "application/x-x509-ca-cert", "class" => "application/octet-stream", "clp" => "application/x-msclip", "cmx" => "image/x-cmx", "cod" => "image/cis-cod", "cpio" => "application/x-cpio", "crd" => "application/x-mscardfile",
            "crl" => "application/pkix-crl", "crt" => "application/x-x509-ca-cert", "csh" => "application/x-csh", "css" => "text/css", "dcr" => "application/x-director", "der" => "application/x-x509-ca-cert", "dir" => "application/x-director", "dll" => "application/x-msdownload", "dms" => "application/octet-stream", "doc" => "application/msword",
            "dot" => "application/msword", "dvi" => "application/x-dvi", "dxr" => "application/x-director", "eps" => "application/postscript", "etx" => "text/x-setext", "evy" => "application/envoy", "exe" => "application/octet-stream", "fif" => "application/fractals", "flr" => "x-world/x-vrml", "gif" => "image/gif",
            "gtar" => "application/x-gtar", "gz" => "application/x-gzip", "h" => "text/plain", "hdf" => "application/x-hdf", "hlp" => "application/winhlp", "hqx" => "application/mac-binhex40", "hta" => "application/hta", "htc" => "text/x-component", "htm" => "text/html", "html" => "text/html",
            "htt" => "text/webviewhtml", "ico" => "image/x-icon", "ief" => "image/ief", "iii" => "application/x-iphone", "ins" => "application/x-internet-signup", "isp" => "application/x-internet-signup", "jfif" => "image/pipeg", "jpe" => "image/jpeg", "jpeg" => "image/jpeg", "jpg" => "image/jpeg",
            "js" => "application/x-javascript", "latex" => "application/x-latex", "lha" => "application/octet-stream", "lsf" => "video/x-la-asf", "lsx" => "video/x-la-asf", "lzh" => "application/octet-stream", "m13" => "application/x-msmediaview", "m14" => "application/x-msmediaview", "m3u" => "audio/x-mpegurl", "man" => "application/x-troff-man",
            "mdb" => "application/x-msaccess", "me" => "application/x-troff-me", "mht" => "message/rfc822", "mhtml" => "message/rfc822", "mid" => "audio/mid", "mny" => "application/x-msmoney", "mov" => "video/quicktime", "movie" => "video/x-sgi-movie", "mp2" => "video/mpeg", "mp3" => "audio/mpeg",
            "mpa" => "video/mpeg", "mpe" => "video/mpeg", "mpeg" => "video/mpeg", "mpg" => "video/mpeg", "mpp" => "application/vnd.ms-project", "mpv2" => "video/mpeg", "ms" => "application/x-troff-ms", "mvb" => "application/x-msmediaview", "nws" => "message/rfc822", "oda" => "application/oda",
            "p10" => "application/pkcs10", "p12" => "application/x-pkcs12", "p7b" => "application/x-pkcs7-certificates", "p7c" => "application/x-pkcs7-mime", "p7m" => "application/x-pkcs7-mime", "p7r" => "application/x-pkcs7-certreqresp", "p7s" => "application/x-pkcs7-signature", "pbm" => "image/x-portable-bitmap", "pdf" => "application/pdf", "pfx" => "application/x-pkcs12",
            "pgm" => "image/x-portable-graymap", "pko" => "application/ynd.ms-pkipko", "pma" => "application/x-perfmon", "pmc" => "application/x-perfmon", "pml" => "application/x-perfmon", "pmr" => "application/x-perfmon", "pmw" => "application/x-perfmon", "png" => "image/png", "pnm" => "image/x-portable-anymap", "pot" => "application/vnd.ms-powerpoint", "ppm" => "image/x-portable-pixmap",
            "pps" => "application/vnd.ms-powerpoint", "ppt" => "application/vnd.ms-powerpoint", "prf" => "application/pics-rules", "ps" => "application/postscript", "pub" => "application/x-mspublisher", "qt" => "video/quicktime", "ra" => "audio/x-pn-realaudio", "ram" => "audio/x-pn-realaudio", "ras" => "image/x-cmu-raster", "rgb" => "image/x-rgb",
            "rmi" => "audio/mid", "roff" => "application/x-troff", "rtf" => "application/rtf", "rtx" => "text/richtext", "scd" => "application/x-msschedule", "sct" => "text/scriptlet", "setpay" => "application/set-payment-initiation", "setreg" => "application/set-registration-initiation", "sh" => "application/x-sh", "shar" => "application/x-shar",
            "sit" => "application/x-stuffit", "snd" => "audio/basic", "spc" => "application/x-pkcs7-certificates", "spl" => "application/futuresplash", "src" => "application/x-wais-source", "sst" => "application/vnd.ms-pkicertstore", "stl" => "application/vnd.ms-pkistl", "stm" => "text/html", "svg" => "image/svg+xml", "sv4cpio" => "application/x-sv4cpio",
            "sv4crc" => "application/x-sv4crc", "t" => "application/x-troff", "tar" => "application/x-tar", "tcl" => "application/x-tcl", "tex" => "application/x-tex", "texi" => "application/x-texinfo", "texinfo" => "application/x-texinfo", "tgz" => "application/x-compressed", "tif" => "image/tiff", "tiff" => "image/tiff",
            "tr" => "application/x-troff", "trm" => "application/x-msterminal", "tsv" => "text/tab-separated-values", "txt" => "text/plain", "uls" => "text/iuls", "ustar" => "application/x-ustar", "vcf" => "text/x-vcard", "vrml" => "x-world/x-vrml", "wav" => "audio/x-wav", "wcm" => "application/vnd.ms-works",
            "wdb" => "application/vnd.ms-works", "wks" => "application/vnd.ms-works", "wmf" => "application/x-msmetafile", "wps" => "application/vnd.ms-works", "wri" => "application/x-mswrite", "wrl" => "x-world/x-vrml", "wrz" => "x-world/x-vrml", "xaf" => "x-world/x-vrml", "xbm" => "image/x-xbitmap", "xla" => "application/vnd.ms-excel",
            "xlc" => "application/vnd.ms-excel", "xlm" => "application/vnd.ms-excel", "xls" => "application/vnd.ms-excel", "xlt" => "application/vnd.ms-excel", "xlw" => "application/vnd.ms-excel", "xof" => "x-world/x-vrml", "xpm" => "image/x-xpixmap", "xwd" => "image/x-xwindowdump", "z" => "application/x-compress", "zip" => "application/zip");
}

/* End of File: S3.php */
