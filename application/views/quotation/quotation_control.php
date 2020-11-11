<div class="row">

  <div class="col-sm-2 col-xs-8 padding-5 margin-bottom-10">
		<label>รหัสสินค้า</label>
    <input type="text" class="form-control input-sm text-center" id="item-code" placeholder="ค้นหารหัสสินค้า" autofocus>
  </div>

	<div class="col-sm-1 col-xs-4 padding-5 margin-bottom-10">
		<label>ราคา</label>
    <input type="number" class="form-control input-sm text-center" id="price">
  </div>

	<div class="col-sm-1 col-xs-4 padding-5 margin-bottom-10">
		<label>ส่วนลด</label>
    <input type="text" class="form-control input-sm text-center" id="disc">
  </div>

  <div class="col-sm-1 col-xs-4 padding-5 margin-bottom-10">
		<label>จำนวน</label>
    <input type="number" class="form-control input-sm text-center" id="qty">
  </div>

  <div class="col-sm-1 col-xs-4 padding-5 margin-bottom-10">
		<label class="display-block not-show">OK</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="addItem()">เพิ่ม</button>
  </div>

  <div class="col-sm-3 hidden-xs">&nbsp; </div>

  <div class="col-sm-2 col-xs-8 padding-5">
		<label>รุ่นสินค้า</label>
    <input type="text" class="form-control input-sm text-center" id="pd-box" placeholder="ค้นรุ่นสินค้า" />
  </div>
  <div class="col-sm-1 col-xs-4 padding-5">
		<label class="display-block not-show">OK</label>
  	<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getProductGrid()"><i class="fa fa-tags"></i> แสดงสินค้า</button>
  </div>
</div>

<input type="hidden" id="item-name" />
<hr class="margin-top-10 padding-5">
<!--- Category Menu ---------------------------------->
<div class='row hidden-xs'>
	<div class='col-sm-12 padding-5'>
		<ul class='nav navbar-nav' role='tablist'>
		<?php echo productTabMenu('order'); ?>
		</ul>
	</div><!---/ col-sm-12 ---->
</div><!---/ row -->
<hr class="padding-5 hidden-xs" />
<div class='row hidden-xs'>
	<div class='col-sm-12'>
		<div class='tab-content' style="min-height:1px; padding:0px; border:0px;">
		<?php echo getProductTabs(); ?>
		</div>
	</div>
</div>
<!-- End Category Menu ------------------------------------>
