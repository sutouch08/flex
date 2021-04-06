<div class="tab-pane fade" id="system">
<?php
    $open     = $CLOSE_SYSTEM == 0 ? 'btn-success' : '';
    $close    = $CLOSE_SYSTEM == 1 ? 'btn-danger' : '';
		$pos_yes = $USE_POS == 1 ? 'btn-primary' : '';
		$pos_no = $USE_POS == 0 ? 'btn-primary' : '';
?>

  <form id="systemForm">
    <div class="row">
  	<?php if( $this->_SuperAdmin ): //---- ถ้ามีสิทธิ์ปิดระบบ ---//	?>
    	<div class="col-sm-3"><span class="form-control left-label">ปิดระบบ</span></div>
      <div class="col-sm-9">
      	<div class="btn-group input-medium">
        	<button type="button" class="btn btn-sm <?php echo $open; ?>" style="width:50%;" id="btn-open" onClick="openSystem()">เปิด</button>
          <button type="button" class="btn btn-sm <?php echo $close; ?>" style="width:50%;" id="btn-close" onClick="closeSystem()">ปิด</button>
        </div>
        <span class="help-block">กรณีปิดระบบจะไม่สามารถเข้าใช้งานระบบได้ในทุกส่วน โปรดใช้ความระมัดระวังในการกำหนดค่านี้</span>
      	<input type="hidden" name="CLOSE_SYSTEM" id="closed" value="<?php echo $CLOSE_SYSTEM; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-3"><span class="form-control left-label">ระบบ POS</span></div>
      <div class="col-sm-9">
      	<div class="btn-group input-medium">
        	<button type="button" class="btn btn-sm <?php echo $pos_yes; ?>" style="width:50%;" id="btn-pos-yes" onClick="togglePOS(1)">เปิด</button>
          <button type="button" class="btn btn-sm <?php echo $pos_no; ?>" style="width:50%;" id="btn-pos-no" onClick="togglePOS(0)">ปิด</button>
        </div>
        <span class="help-block">เปิด/ปิด การใช้งานระบบ POS</span>
      	<input type="hidden" name="USE_POS" id="use_pos" value="<?php echo $USE_POS; ?>" />
      </div>
      <div class="divider-hidden"></div>
    <?php endif; ?>



      <div class="col-sm-9 col-sm-offset-3">
      	<button type="button" class="btn btn-sm btn-success" onClick="updateConfig('systemForm')"><i class="fa fa-save"></i> บันทึก</button>
      </div>
      <div class="divider-hidden"></div>

    </div><!--/row-->
  </form>
</div><!--/ tab pane -->
