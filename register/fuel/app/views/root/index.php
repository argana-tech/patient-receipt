<div class="display">
  <div class="current">
    <div class="headline">診察券を読み取ります。<br />
      カードリーダーにカードを通してください。</div>
  </div>
  <!-- / .current -->
  <?php echo Form::open(array('action' => 'detail', 'method' => 'post', 'name' => 'readerform'));?>
  <div class="figure"> <img src="<?php echo Uri::create('assets/img/cardreader.png'); ?>" alt=""> </div>
  <!-- / .figure -->
  <input type="text" name="patient_id" value="" onKeyUp="readCard(this)" style="ime-inactive;position:fixed;left:-1000px;border:none;color:none;" autocomplete="off">
  <?php echo Form::close();?>
</div>
<!-- / .display -->

<script>
  // フォーカス
  document.readerform.patient_id.focus();

  setInterval(function(){
    document.readerform.patient_id.focus();
  }, 1000);

  // 読みとりイベント
  function readCard($this) {
    var id = $this.value;
    if (id.length == 8) {
      console.log(id);
      document.readerform.submit();
    }
  }

</script>