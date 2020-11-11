<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo $this->home; ?>/add">
<div class="row">
  <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm" value="" disabled />
  </div>

  <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" name="date" id="date" value="<?php echo date('d-m-Y'); ?>" required readonly />
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>รหัสลูกค้า</label>
    <input type="text" class="form-control input-sm" name="customer" id="customer" value="" autofocus required />
  </div>

  <div class="col-sm-4 col-xs-6 padding-5">
    <label>ชื่อลูกค้า</label>
    <input type="text" class="form-control input-sm" name="customerName" id="customerName" value="" required />
  </div>

	<div class="col-sm-2 col-xs-6 padding-5">
    <label>ลูกค้า[ออนไลน์]</label>
		<input type="text" class="form-control input-sm" name="cust_ref" value="" />
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>ใบเสนอราคา</label>
		<input type="text" class="form-control input-sm" name="qt_no" id="qt_no" value="" />
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>ช่องทางขาย</label>
		<select class="form-control input-sm" name="channels" required>
			<option value="">ทั้งหมด</option>
			<?php echo select_channels(); ?>
		</select>
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>การชำระเงิน</label>
		<select class="form-control input-sm" name="payment" id="payment" required>
			<option value="">ทั้งหมด</option>
			<?php echo select_payment_method(); ?>
		</select>
  </div>

  <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>อ้างอิงออเดอร์</label>
		<input type="text" class="form-control input-sm" name="reference" value="" />
  </div>

	<div class="col-sm-2 col-xs-6 padding-5">
		<label>การจัดส่ง</label>
    <select class="form-control input-sm" name="sender_id" id="sender_id">
      <option value="">กรุณาเลือก</option>
      <?php echo select_sender_list(); ?>
    </select>
  </div>
  <div class="col-sm-4  col-4-harf col-xs-12 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" name="remark" id="remark" value="">
  </div>
  <div class="col-sm-1 col-xs-12 padding-5">
    <label class="display-block not-show">Submit</label>
    <button type="submit" class="btn btn-xs btn-success btn-block"><i class="fa fa-plus"></i> เพิ่ม</button>
  </div>
</div>
<input type="hidden" name="customerCode" id="customerCode" value="" />
</form>
<hr class="margin-top-15 padding-5">

<script src="<?php echo base_url(); ?>scripts/orders/orders.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_add.js"></script>

<?php $this->load->view('include/footer'); ?>
