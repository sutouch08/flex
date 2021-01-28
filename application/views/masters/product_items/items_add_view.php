<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="margin-bottom-15"/>
<div class="row">
	<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/add"; ?>">
	<div class="row">
		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">รหัส</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="code" id="code" class="width-100" maxlength="50" value="" onkeyup="validCode(this)" autofocus required />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline grey" id="code-error">Allow only [a-z, A-Z, 0-9, "-", "_" ]</div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ชื่อ</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="name" id="name" class="width-100" maxlength="100" value="" required />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">รุ่น</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="style" id="style" class="width-100" value="" />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="style-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">สี</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="color" id="color" class="width-100" value="" />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="color-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ไซส์</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="size" id="size" class="width-100" value="" />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="size-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">บาร์โค้ด</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="barcode" id="barcode" class="width-100" value="" />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="barcode-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ราคาทุน(ไม่รวม VAT)</label>
			<div class="col-sm-1 col-1-harf col-xs-12">
				<input type="number" step="any" name="cost" id="cost" class="width-100 text-right" value="0.00" />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="cost-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ราคาขาย(รวม VAT)</label>
			<div class="col-sm-1 col-1-harf col-xs-12">
				<input type="number" step="any" name="price" id="price" class="width-100 text-right" value="0.00" />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="price-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">หน่วยนับ</label>
			<div class="col-xs-12 col-sm-3">
				<select class="form-control input-sm" name="unit_code" id="unit_code">
					<?php echo select_unit(); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="unit-error"></div>
		</div>

		<?php if(getConfig('USE_VAT')) : ?>
		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">VAT</label>
			<div class="col-xs-12 col-sm-3">
				<select class="form-control input-sm" name="vat_code" id="vat_code">
					<?php echo select_vat_group(); ?>
				</select>
			</div>
		</div>
		<?php else : ?>
			<input type="hidden" name="vat_code" id="vat_code" value="" />
		<?php endif;?>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ยี่ห้อ</label>
			<div class="col-xs-12 col-sm-3">
				<select name="brand_code" id="brand" class="form-control">
					<option value="">โปรดเลือก</option>
				<?php echo select_product_brand(); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="brand-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">กลุ่มสินค้า</label>
			<div class="col-xs-12 col-sm-3">
				<select name="group_code" id="group" class="form-control input-sm">
					<option value="">โปรดเลือก</option>
				<?php echo select_product_group(); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="group-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">กลุ่มย่อย</label>
			<div class="col-xs-12 col-sm-3">
				<select name="sub_group_code" id="subGroup" class="form-control">
					<option value="">โปรดเลือก</option>
				<?php echo select_product_sub_group(); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="subGroup-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">หมวดหมู่สินค้า</label>
			<div class="col-xs-12 col-sm-3">
				<select name="category_code" id="category" class="form-control">
					<option value="">โปรดเลือก</option>
				<?php echo select_product_category(); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="category-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ประเภทสินค้า</label>
			<div class="col-xs-12 col-sm-3">
				<select name="kind_code" id="kind" class="form-control" >
					<option value="">โปรดเลือก</option>
				<?php echo select_product_kind(); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="kind-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ชนิดสินค้า</label>
			<div class="col-xs-12 col-sm-3">
				<select name="type_code" id="type" class="form-control" >
					<option value="">โปรดเลือก</option>
				<?php echo select_product_type(); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="type-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ปี</label>
			<div class="col-xs-12 col-sm-3">
				<select name="year" id="year" class="form-control">
				<?php echo select_years(date('Y')); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="year-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">นับสต็อก</label>
			<div class="col-xs-12 col-sm-3">
				<label style="padding-top:5px;">
					<input name="count_stock" id="count_stock" class="ace ace-switch ace-switch-7" type="checkbox" value="1" checked />
					<span class="lbl"></span>
				</label>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">อนุญาติให้ขาย</label>
			<div class="col-xs-12 col-sm-3">
				<label style="padding-top:5px;">
					<input name="can_sell" id="can_sell" class="ace ace-switch ace-switch-7" type="checkbox" value="1" checked />
					<span class="lbl"></span>
				</label>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red"></div>
		</div>


		<div class="form-group hide">
			<label class="col-sm-3 control-label no-padding-right">API</label>
			<div class="col-xs-12 col-sm-3">
				<label style="padding-top:5px;">
					<input name="is_api" id="is_api" class="ace ace-switch ace-switch-7" type="checkbox" value="1" />
					<span class="lbl"></span>
				</label>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ใช้งาน</label>
			<div class="col-xs-12 col-sm-3">
				<label style="padding-top:5px;">
					<input name="active" id="active" class="ace ace-switch ace-switch-7" type="checkbox" value="1" checked />
					<span class="lbl"></span>
				</label>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label not-show">บันทึก</label>
			<div class="col-xs-12 col-sm-3">
				<button type="button" class="btn btn-sm btn-success btn-block" onclick="checkAdd()"><i class="fa fa-save"></i> บันทึก</button>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red"></div>
		</div>

	</div>
	</form>
</div><!--/ row  -->

<script src="<?php echo base_url(); ?>scripts/masters/items.js"></script>

<?php $this->load->view('include/footer'); ?>
