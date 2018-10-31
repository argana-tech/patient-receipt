<div class="display">
  <div class="description">
    <div class="figure"> <img src="<?php echo Uri::create('qrimage/:id', array('id' => $this->unique_id)); ?>" alt=""></div>
    <!-- / .figure -->
    <div class="guidance">スマートフォンから「とりりん」を起動し、上のQRコードを読み取ってください。</div>
    <div class="illust"><img src="<?php echo Uri::create('assets/img/tap_illust.png'); ?>" alt=""></div>
    <div class="guide">読み取りが完了したら、完了ボタンをクリックしてください。<br />
      <span class="time_left_sec">20</span>秒後に自動でトップ画面へ移動します。 </div>
  </div>
  <div class="confirm"> <a href="<?php echo Uri::create(''); ?>" class="btn">完了</a></div>
</div>
<!-- / .display -->
<script type="text/javascript">
var startTime = Date.now();
var timeCountDown = <?php echo Config::get('my.redirect_top_time');?>;

function redirectTop(){
    var timeLeft = timeCountDown - (Date.now() - startTime);
    var sec = Math.floor(timeLeft / 1000);
    if (timeLeft >= 0) {
    $('.time_left_sec').text(sec);
    }

    if (timeLeft <= 0) {
        location.href = "<?php echo Uri::create(''); ?>";
        return;
    }

    setTimeout("redirectTop()", 100);
}

$(document).ready(function() {
    redirectTop();
});
</script>