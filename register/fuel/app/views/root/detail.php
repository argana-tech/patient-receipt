<div id="loading">
  <div class="back"></div>
  <div class="text">読み込み中です。しばらくお待ちください。<br><img src="<?php echo Uri::create('assets/img/loading.gif');?>"></div>
</div>

<div class="display">
  <?php echo Form::open(array('action' => 'qr', 'method' => 'post'));?>
  <div class="description">
    <div>下記の情報を確認してください。</div>
    <table>
      <tr>
        <th>氏名</th>
        <td id="text_name">-</td>
      </tr>
      <tr>
        <th>生年月日</th>
        <td id="text_birth_at">-</td>
      </tr>
    </table>
    よろしければ、確認ボタンを押してください。 </div>
  <div class="confirm"> <button type="submit" value="確認" class="btn">確認</button>
  <a href="/" class="btn btn-back">戻る</a>

  <?php echo Form::hidden('unique_id', '');?>
  <?php echo Form::hidden('patient_id', $this->patient_id);?>
  <?php echo Form::hidden('name', '');?>
  <?php echo Form::hidden('birth_at', '');?>
  <?php echo Form::close();?>
</div>
<!-- / .display -->

<script type="text/javascript">
$(document).ready(function() {
  $.ajax({
    url: '<?php echo Uri::create('api/get_patient_detail/:patient_id', array('patient_id' => $this->patient_id)); ?>?<?php echo time();?>',
    type:'GET',
    dataType: 'json',
    timeout:60000,
  }).then(function(data){
    if (data.is_success) {
        $('input[name=unique_id]').val(data.unique_id);
        $('input[name=name]').val(data.name);
        $('input[name=birth_at]').val(data.birth_at);

        $('#text_name').text(data.name);
        $('#text_birth_at').text(data.birth_at);
        $('#loading').hide();
    } else {
      alert(data.error_message);
      location.href = '<?php echo Uri::create(''); ?>';
    }
      console.log(data);
  }).fail(function(XMLHttpRequest, textStatus, errorThrown) {
    alert('エラーが発生しました。');
    location.href = '<?php echo Uri::create(''); ?>';
  });
});
</script>