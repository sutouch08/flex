<?php $required = getConfig('REQUIRED_ALL_ATTRIBUTE') == 1 ? 'required' : ''; ?>
<style>
.lbl::before {
	margin-right:5px !important;
}
</style>
<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/update_style"; ?>">
<div class="row">
	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">รหัสรุ่นสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<input type="text" class="width-100" value="<?php echo $style->code; ?>" disabled/>
			<input type="hidden" name="code" id="code" value="<?php echo $style->code; ?>" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ชื่อรุ่นสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<input type="text" name="name" id="name" class="width-100" maxlength="100" value="<?php echo $style->name; ?>" required />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ทุน</label>
		<div class="col-xs-8 col-sm-3">
			<input type="number" step="any" name="cost" id="cost" class="width-100 text-right" value="<?php echo $style->cost; ?>" />
		</div>
		<div class="col-sm-3 col-xs-3">
			<label>
				<input type="checkbox" class="ace" id="cost-update" name="cost_update" value="Y"/>
				<span class="lbl">  อัพเดตทุนในรายการด้วย</span>
			</label>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="cost-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ราคา</label>
		<div class="col-xs-12 col-sm-3">
			<input type="number" step="any" name="price" id="price" class="width-100 text-right" value="<?php echo $style->price; ?>" />
		</div>
		<div class="col-sm-3 col-xs-3">
			<label>
				<input type="checkbox" class="ace" id="price-update" name="price_update" value="Y"/>
				<span class="lbl">  อัพเดตราคาในรายการด้วย</span>
			</label>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="price-error"></div>

	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">หน่วยนับ</label>
		<div class="col-xs-12 col-sm-3">
			<select class="form-control input-sm" name="unit_code" id="unit_code">
				<?php echo select_unit($style->unit_code); ?>
			</select>
		</div>
		<div class="col-sm-2 col-xs-4 padding-5">
			<button type="button" class="btn btn-xs btn-success btn-block" onclick="addAttribute('unit_code')"><i class="fa fa-plus"></i></button>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="unit-error"></div>
	</div>

	<?php if(getConfig('USE_VAT')) : ?>
	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">VAT</label>
		<div class="col-xs-12 col-sm-3">
			<select class="form-control input-sm" name="vat_code" id="vat_code">
				<?php echo select_vat_group($style->vat_code); ?>
			</select>
		</div>
	</div>
	<?php else : ?>
		<input type="hidden" name="vat_code" id="vat_code" value="" />
	<?php endif;?>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ยี่ห้อ</label>
		<div class="col-xs-12 col-sm-3">
			<select name="brand_code" id="brand" class="form-control" <?php echo $required; ?>>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_brand($style->brand_code); ?>
			</select>
		</div>
		<div class="col-sm-2 col-xs-4 padding-5">
			<button type="button" class="btn btn-xs btn-success btn-block" onclick="addAttribute('brand')"><i class="fa fa-plus"></i></button>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="brand-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">กลุ่มสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="group_code" id="group" class="form-control" <?php echo $required; ?>>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_group($style->group_code); ?>
			</select>
		</div>
		<div class="col-sm-2 col-xs-4 padding-5">
			<button type="button" class="btn btn-xs btn-success btn-block" onclick="addAttribute('group')"><i class="fa fa-plus"></i></button>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="group-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">กลุ่มย่อยสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="sub_group_code" id="subGroup" class="form-control" <?php echo $required; ?>>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_sub_group($style->sub_group_code); ?>
			</select>
		</div>
		<div class="col-sm-2 col-xs-4 padding-5">
			<button type="button" class="btn btn-xs btn-success btn-block" onclick="addAttribute('subGroup')"><i class="fa fa-plus"></i></button>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="subGroup-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">หมวดหมู่สินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="category_code" id="category" class="form-control" <?php echo $required; ?>>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_category($style->category_code); ?>
			</select>
		</div>
		<div class="col-sm-2 col-xs-4 padding-5">
			<button type="button" class="btn btn-xs btn-success btn-block" onclick="addAttribute('category')"><i class="fa fa-plus"></i></button>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="category-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ประเภทสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="kind_code" id="kind" class="form-control" <?php echo $required; ?>>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_kind($style->kind_code); ?>
			</select>
		</div>
		<div class="col-sm-2 col-xs-4 padding-5">
			<button type="button" class="btn btn-xs btn-success btn-block" onclick="addAttribute('kind')"><i class="fa fa-plus"></i></button>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="kind-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ชนิดสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="type_code" id="type" class="form-control" <?php echo $required; ?>>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_type($style->type_code); ?>
			</select>
		</div>
		<div class="col-sm-2 col-xs-4 padding-5">
			<button type="button" class="btn btn-xs btn-success btn-block" onclick="addAttribute('type')"><i class="fa fa-plus"></i></button>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="type-error"></div>
	</div>


	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ปีสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="year" id="year" class="form-control" <?php echo $required; ?>>
				<option value="">กรุณาเลือก</option>
			<?php echo select_years($style->year); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="year-error"></div>
	</div>


	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">แถบแสดงสินค้า</label>
		<div class="col-xs-12 col-sm-reset">
			<?php echo productTabsTree($style->code); ?>
		</div>
	</div>


	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">นับสต็อก</label>
		<div class="col-xs-12 col-sm-3">
			<label style="padding-top:5px;">
				<input name="count_stock" class="ace ace-switch ace-switch-7" type="checkbox" value="1" <?php echo is_checked($style->count_stock, 1); ?> />
				<span class="lbl"></span>
			</label>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">อนุญาติให้ขาย</label>
		<div class="col-xs-12 col-sm-3">
			<label style="padding-top:5px;">
				<input name="can_sell" class="ace ace-switch ace-switch-7" type="checkbox" value="1" <?php echo is_checked($style->can_sell, 1); ?> />
				<span class="lbl"></span>
			</label>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red"></div>
	</div>


	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">เปิดใช้งาน</label>
		<div class="col-xs-12 col-sm-3">
			<label style="padding-top:5px;">
				<input name="active" class="ace ace-switch ace-switch-7" type="checkbox" value="1" <?php echo is_checked($style->active, 1); ?> />
				<span class="lbl"></span>
			</label>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label not-show">บันทึก</label>
		<div class="col-xs-12 col-sm-3">
			<button type="submit" class="btn btn-sm btn-success btn-block"><i class="fa fa-save"></i> บันทึก</button>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red"></div>
	</div>
</div>

<input type="hidden" id="style" value="<?php echo $style->code; ?>" />
</form>

<?php $this->load->view('masters/products/attribute_modal'); ?>
