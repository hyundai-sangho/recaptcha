<?php

/* php dotenv 사용을 위해 vendor 폴더 내부의 autoload.php require 함. */
require_once "vendor/autoload.php";

/* php dotenv 사용법 */
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 구글 리캡챠 사이트 키값을 입력한다.
$sitekey = $_ENV['SITEKEY'];

?>

<!-- 스타일 -->
<style>
  .form {
    margin: 30px auto;
    border: solid 1px #eee;
    max-width: 400px;
    width: 100%;
    padding: 1%;
  }

  .form .submit {
    margin: 20px 0;
  }
</style>

<div class="form">
  <form id="server" action="server.php" method="post">
    <input type="hidden" name="recatchaResponse" value="">
    <div class="recaptcha">
      <div class="g-recaptcha" data-sitekey="<?php echo $sitekey ?>"></div>
    </div>
    <div class="submit">
      <input type="submit" value="등록">
    </div>
  </form>

  <!-- jquery 3.5.1 -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

  <!-- 리캽챠 API -->
  <script src="https://www.google.com/recaptcha/api.js"></script>


  <!-- 스크립트 -->
  <script>
    var authSubmit = true;

    $(document).on('submit', '#server', function () {
      if (authSubmit !== true) {
        alert("잠시만 기다려 주세요.");

        return false;
      }

      var recatchaResponse = grecaptcha.getResponse()

      if (!recatchaResponse) {
        alert("로봇이 아닙니다에 체크해주세요.");

        return false;
      }

      var $server = $('#server');
      $server.find('[name="recatchaResponse"]').val(recatchaResponse);
      authSubmit = false;

      // ajax 통신
      $.ajax(
        {
          url: $server.attr('action'),
          type: $server.attr('method'),
          dataType: 'json',
          data: $server.serialize()
        }
      )
        .done(function (e) {
          alert(e.msg);

          return false;
        })
        .fail(function (e) {
          alert(e.responseText);
        })
        .always(function () {
          authSubmit = true;
          grecaptcha.reset(); // 리캡챠 초기화

          $server.find('[name="recatchaResponse"]').val('');
        })

      return false;
    });
  </script>
</div>
