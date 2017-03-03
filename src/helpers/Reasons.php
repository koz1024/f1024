<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\helpers;

/**
 * HTTP State codes
 */
class Reasons{

    public function __construct() {
        return [
            "100" => "Continue",
            "101" => "Switching Protocols",
            "102" => "Processing",
            "105" => "Name Not Resolved",

            "200" =>  "OK",
            "201" => "Created",
            "202" => "Accepted",
            "203" => "Non-Authoritative Information",
            "204" => "No Content",
            "205" => "Reset Content",
            "206" => "Partial Content",
            "207" => "Multi-Status",
            "226" => "IM Used",

            "300" => "Multiple Choises",
            "301" => "Moved Permanently",
            "302" => "Moved Temporarily",
            "303" => "See Other",
            "304" => "Not Modified",
            "305" => "Use Proxy",
            "306" => "",
            "307" => "Temporary Redirect",

            "400" => "Bad Request",
            "401" => "Unauthorized",
            "402" => "Payment Required",
            "403" => "Forbidden",
            "404" => "Not Found" ,
            "405" => "Method Not Allowed",
            "406" => "Not Acceptable",
            "407" => "Proxy Authentication Required",
            "408" => "Request Timeout",
            "409" => "Conflict",
            "410" => "Gone",
            "411" => "Length Required",
            "412" => "Precondition Failed",
            "413" => "Request Entity Too Large",
            "414" => "Request-URI Too Large",
            "415" => "Unsupported Media Type",
            "416" => "Requested Range Not Satisfiable",
            "417" => "Expectation Failed",
            "418" => "I'm a teapot",
            "422" => "Unprocessable Entity",
            "423" => "Locked",
            "424" => "Failed Dependency",
            "425" => "Unordered Collection ",
            "426" => "Upgrade Required",
            "428" => "Precondition Required",
            "429" => "Too Many Requests",
            "431" => "Request Header Fields Too Large",
            "434" => "Requested host unavailable",
            "449" => "Retry With",
            "451" => "Unavailable For Legal Reasons",
            "456" => "Unrecoverable Error", 
            "499" => "",

            "500" => "Internal Server Error",
            "501" => "Not Implemented",
            "502" => "Bad Gateway",
            "503" => "Service Unavailable",
            "504" => "Gateway Timeout",
            "505" => "HTTP Version Not Supported",
            "506" => "Variant Also Negotiates",
            "507" => "Insufficient Storage",
            "508" => "Loop Detected",
            "509" => "Bandwidth Limit Exceeded",
            "510" => "Not Extended",
            "511" => "Network Authentication Required "
        ];
    }
}