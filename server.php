<?php

/* php dotenv 사용을 위해 vendor 폴더 내부의 autoload.php require 함. */
require_once "vendor/autoload.php";

/* php dotenv 사용법 */
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 리캽챠 서버단 프로그램 예제

// 구글 리캡챠 비밀키 값을 입력한다.
$secret_key = $_ENV['SECRET_KEY'];

extract($_POST);

if (empty($recatchaResponse)) {

  die(
    json_encode(
      array(
        'rst' => 'fail',
        'msg' => '`로봇이 아닙니다.`에 체크해주세요.'
      )
    )
  );
}

// API URL 데이터
$apiData = array(
  "secret" => $secret_key,
  "response" => $recatchaResponse,
  "remoteip" => $_SERVER['REMOTE_ADDR'],
);

// API URL 셋팅
$url = "https://www.google.com/recaptcha/api/siteverify?" . http_build_query($apiData);

// CURL 통신
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_REFERER, 'http' . (!empty($_SERVER['HTTPS']) ? 's' : null) . '://' . $_SERVER['HTTP_HOST']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 0);

$response = curl_exec($ch);

if (curl_error($ch)) {
  $response = array(
    'curl_error' => true,
    'msg' => 'CURL Error(' . curl_errno($ch) . ') ' . curl_error($ch)
  );
}
curl_close($ch);

// 통신 에러 처리
if (isset($response['curl_error'])) {
  die(
    json_encode(
      array(
        'rst' => 'fail',
        'msg' => $response['msg']
      )
    )
  );
}

if (empty($response)) {
  die(
    json_encode(
      array(
        'rst' => 'fail',
        'msg' => '응답에러'
      )
    )
  );
}

$responseData = json_decode($response, true);

if (empty($responseData['success']) || $responseData['success'] !== true) {
  die(
    json_encode(
      array(
        'rst' => 'fail',
        'msg' => '인증 실패'
      )
    )
  );
} else {
  die(
    json_encode(
      array(
        'rst' => 'success',
        'msg' => '인증 성공!'
      )
    )
  );
}
