<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/add"; ?>">

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">รหัส</label>
    <div class="col-xs-12 col-sm-3">
      <input type="text" name="code" id="code" class="width-100 code" maxlength="15" value="<?php echo $code; ?>" onkeyup="validCode(this)" autofocus required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>



  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="name" id="name" class="width-100" maxlength="200" value="<?php echo $name; ?>" required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ID/Tax ID</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="Tax_id" id="Tax_id" class="width-100" value="<?php echo $Tax_Id; ?>" />
    </div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">กลุ่มลูกค้า</label>
    <div class="col-xs-12 col-sm-3">
			<select name="group" id="group" class="form-control" required>
				<option value="">เลือก</option>
				<?php echo select_customer_group($group); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="group-error"></div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ประเภทลูกค้า</label>
    <div class="col-xs-12 col-sm-3">
			<select name="kind" id="kind" class="form-control" required>
				<option value="">เลือก</option>
				<?php echo select_customer_kind($kind); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="kind-error"></div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชนิดลูกค้า</label>
    <div class="col-xs-12 col-sm-3">
			<select name="type" id="type" class="form-control" required>
				<option value="">เลือก</option>
				<?php echo select_customer_type($type); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="type-error"></div>
  </div>



	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">เกรดลูกค้า</label>
    <div class="col-xs-12 col-sm-3">
			<select name="class" id="class" class="form-control" required>
				<option value="">เลือก</option>
				<?php echo select_customer_class($class); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="class-error"></div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">พื้นที่ขาย</label>
    <div class="col-xs-12 col-sm-3">
			<select name="area" id="area" class="form-control" required>
				<option value="">เลือก</option>
				<?php echo select_customer_area($area); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="area-error"></div>
  </div>

	<div class="form-group">
	 <label class="col-sm-3 control-label no-padding-right">พนักงานขาย</label>
	 <div class="col-xs-12 col-sm-3">
		 <select name="sale" id="sale" class="form-control">
			 <?php echo select_sale($sale); ?>
		 </select>
	 </div>
	</div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">เครดิตเทอม</label>
    <div class="col-xs-12 col-sm-3">
			<input type="number" name="credit_term" id="credit_term" class="width-50" value="<?php echo $credit_term; ?>" />
			วัน
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">วงเงินเครดิต</label>
    <div class="col-xs-12 col-sm-3">
			<input type="number" name="CreditLine" id="CreditLine" class="width-50" value="<?php echo $credit; ?>" />
    </div>
  </div>


  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">หมายเหตุ</label>
    <div class="col-xs-12 col-sm-3">
      <textarea class="form-control input-sm" name="note" id="note" rows="10"><?php echo $note; ?></textarea>

    </div>
  </div>




	<div class="divider-hidden"></div>
  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
      <p class="pull-right">
        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Save</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>
</form>

<script src="<?php echo base_url(); ?>scripts/masters/customers.js"></script>
<?php $this->load->view('include/footer'); ?>
