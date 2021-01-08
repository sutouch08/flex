<form class="form-horizontal">

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">รหัส</label>
    <div class="col-xs-12 col-sm-4">
      <input type="text" class="form-control input-sm" value="<?php echo $ds->code; ?>" disabled/>
    </div>
  </div>



  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-4">
			<input type="text" class="form-control input-sm" value="<?php echo $ds->name; ?>" disabled />
    </div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ID/Tax ID</label>
    <div class="col-xs-12 col-sm-4">
			<input type="text" class="form-control input-sm" value="<?php echo $ds->Tax_Id; ?>" disabled/>
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">กลุ่มลูกค้า</label>
    <div class="col-xs-12 col-sm-4">
			<select class="form-control" disabled>
				<option value="">เลือก</option>
				<?php echo select_customer_group($ds->group_code); ?>
			</select>
    </div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ประเภทลูกค้า</label>
    <div class="col-xs-12 col-sm-4">
			<select class="form-control" disabled>
				<option value="">เลือก</option>
				<?php echo select_customer_kind($ds->kind_code); ?>
			</select>
    </div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชนิดลูกค้า</label>
    <div class="col-xs-12 col-sm-4">
			<select class="form-control" disabled>
				<option value="">เลือก</option>
				<?php echo select_customer_type($ds->type_code); ?>
			</select>
    </div>
  </div>



	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">เกรดลูกค้า</label>
    <div class="col-xs-12 col-sm-4">
			<select class="form-control" disabled>
				<option value="">เลือก</option>
				<?php echo select_customer_class($ds->class_code); ?>
			</select>
    </div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">พื้นที่ขาย</label>
    <div class="col-xs-12 col-sm-4">
			<select class="form-control" disabled>
				<option value="">เลือก</option>
				<?php echo select_customer_area($ds->area_code); ?>
			</select>
    </div>
  </div>


	<div class="form-group">
	 <label class="col-sm-3 control-label no-padding-right">พนักงานขาย</label>
	 <div class="col-xs-12 col-sm-4">
		 <select class="form-control" disabled>
			 <?php echo select_sale($ds->sale_code); ?>
		 </select>
	 </div>
	</div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">เครดิตเทอม</label>
    <div class="col-xs-12 col-sm-4">
			<input type="number" class="form-control input-sm width-50" value="<?php echo $ds->credit_term; ?>" disabled/>
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">วงเงินเครติด</label>
    <div class="col-xs-12 col-sm-4">
			<input type="text" class="form-control input-sm width-50" value="<?php echo number($ds->amount,2); ?>" disabled/>
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">วงเงินใช้ไป</label>
    <div class="col-xs-12 col-sm-4">
			<input type="text" class="form-control input-sm width-50" value="<?php echo number($ds->used,2); ?>" disabled/>
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">วงเงินคงเหลือ</label>
    <div class="col-xs-12 col-sm-4">
			<input type="text" class="form-control input-sm width-50 " value="<?php echo number($ds->balance, 2); ?>" disabled/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">หมายเหตุ</label>
    <div class="col-xs-12 col-sm-4">
      <textarea class="form-control input-sm" name="note" id="note" rows="10" disabled><?php echo $ds->note; ?></textarea>

    </div>
  </div>

	<div class="divider-hidden">

	</div>
  <input type="hidden" name="customers_code" id="customers_code" value="<?php echo $ds->code; ?>" />
	<input type="hidden" name="customers_name" value="<?php echo $ds->name; ?>" />
</form>
