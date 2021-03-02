<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><i class="fa fa-cubes"></i> <?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6 col-xs-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 padding-top-20"/>

<form class="form-horizontal">
	<input type="hidden" name="code" id="code" value="<?php echo $code; ?>" />
	<input type="hidden" name="old_name" id="old_name" value="<?php echo $name; ?>" />
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">รหัส</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text"  class="form-control input-sm" value="<?php echo $code; ?>" disabled />
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="name" id="name" maxlength="250" class="form-control input-sm" value="<?php echo $name; ?>" required />
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">โซน</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="zone" id="zone" maxlength="250" class="form-control input-sm" value="<?php echo $zone_name; ?>"  />
			<input type="hidden" name="zone_code" id="zone_code" value="<?php echo $zone_code; ?>" />
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ลูกค้า</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="customer" id="customer" maxlength="250" class="form-control input-sm" value="<?php echo $customer_name; ?>"  />
			<input type="hidden" name="customer_code" id="customer_code" value="<?php echo $customer_code; ?>" />
    </div>
  </div>

<?php $btn_yes = $active == 1 ? 'btn-success' : ''; ?>
<?php $btn_no = $active == 0 ? 'btn-danger' : ''; ?>
	<div class="form-group">
 	 <label class="col-sm-3 control-label no-padding-right">เปิดใช้งาน</label>
 	 <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
 		<div class="btn-group width-100">
 			<button type="button" class="btn btn-sm width-50 <?php echo $btn_yes; ?>" id="btn-active-yes" onclick="toggleActive(1)">ใช่</button>
			<button type="button" class="btn btn-sm width-50 <?php echo $btn_no; ?>" id="btn-active-no" onclick="toggleActive(0)">ไม่ใช่</button>
 		</div>
 	 </div>
  </div>

	<input type="hidden" id="active" name="active" value="<?php echo $active; ?>" />

<?php if($this->pm->can_edit) : ?>
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right hidden-xs">&nbsp;</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<button type="button" class="btn btn-sm btn-success pull-right" id="btn-save" onclick="update()"><i class="fa fa-save"></i> Update</button>
    </div>
  </div>
<?php endif; ?>

</form>

<script src="<?php echo base_url(); ?>scripts/masters/shop.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
