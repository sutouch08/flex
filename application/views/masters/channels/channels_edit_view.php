<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6 col-xs-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/update"; ?>">

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">รหัส</label>
    <div class="col-xs-12 col-sm-3">
			<span class="input-icon input-icon-right width-100">
      	<input type="text" name="code" id="code" class="form-control input-sm code" value="<?php echo $data->code; ?>" autofocus required />
				<i class="ace-icon fa fa-user"></i>
			</span>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>



  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-3">
			<span class="input-icon input-icon-right width-100">
        <input type="text" name="name" id="name" class="form-control input-sm" value="<?php echo $data->name; ?>" required />
				<i class="ace-icon fa fa-user"></i>
			</span>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ลูกค้าเริ่มต้น</label>
    <div class="col-xs-12 col-sm-3">
			<span class="input-icon input-icon-right width-100">
        <input type="text" name="customer_name" id="customer_name" class="form-control input-sm" value="<?php echo $data->customer_name; ?>" />
				<i class="ace-icon fa fa-user"></i>
			</span>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="divider-hidden">

	</div>
  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
      <p class="pull-right">
        <button type="submit" class="btn btn-xs btn-success"><i class="fa fa-save"></i> Save</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>
	<input type="hidden" name="channels_code" id="channels_code" value="<?php echo $data->code; ?>" />
  <input type="hidden" name="channels_name" id="channels_name" value="<?php echo $data->name; ?>" />
	<input type="hidden" name="customer_code" id="customer_code" value="<?php echo $data->customer_code; ?>" />
</form>

<script src="<?php echo base_url(); ?>scripts/masters/channels.js"></script>
<?php $this->load->view('include/footer'); ?>
