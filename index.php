<?php
/**
 * path to file
 */
$path = '';

/**
 * token generated from profile
 */
$token = "";

/**
 * document type 'ktp', 'npwp', 'sim-2019'
 */
$type = 'ktp';

/**
 * url
 */
$url = "https://api.aksarakan.com/document/$type";

/**
 * curl execution
 */
$curl = curl_init();

$boundary = uniqid("", true);

$delimiter = '-------------' . $boundary;
$fileContent = file_get_contents($path);

$postData = buildDataFiles($boundary, $fileContent, basename($path));

curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "PUT",
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => $postData,
    CURLOPT_HTTPHEADER => array(
        "Authentication: Bearer $token",
        "Content-Type: multipart/form-data; boundary=" . $delimiter,
        "Content-Length: " . strlen($postData)

    ),
));


//
$response = curl_exec($curl);

if($response) {

    var_dump(json_decode($response, true));

} else {

    $info = curl_getinfo($curl);
    echo "code: ${info['http_code']}";

    var_dump($info['request_header']);

    $err = curl_error($curl);
    echo "error";
    var_dump($err);
}

curl_close($curl);

/**
 * build boundary
 *
 * @param $boundary
 * @param $content
 * @param $fileName
 * @return string
 */
function buildDataFiles($boundary, $content, $fileName){
    $data = '';
    $eol = "\r\n";

    $delimiter = '-------------' . $boundary;

    $data .= "--" . $delimiter . $eol
        . 'Content-Disposition: form-data; name="file"; filename="' . $fileName . '"' . $eol
        //. 'Content-Type: image/png'.$eol
        . 'Content-Transfer-Encoding: binary'.$eol
    ;

    $data .= $eol;
    $data .= $content . $eol;

    $data .= "--" . $delimiter . "--".$eol;

    return $data;
}
