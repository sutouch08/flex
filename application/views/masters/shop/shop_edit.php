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
	<input type="hidden" name="shop_id" id="shop_id" value="<?php echo $shop->id; ?>" />
	<input type="hidden" name="code" id="code" value="<?php echo $shop->code; ?>" />
	<input type="hidden" name="old_name" id="old_name" value="<?php echo $shop->name; ?>" />
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">รหัส</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text"  class="form-control input-sm" value="<?php echo $shop->code; ?>" disabled />
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="name" id="name" maxlength="250" class="form-control input-sm" value="<?php echo $shop->name; ?>" required />
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">โซน</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="zone" id="zone" maxlength="250" class="form-control input-sm" value="<?php echo $shop->zone_name; ?>"  />
			<input type="hidden" name="zone_code" id="zone_code" value="<?php echo $shop->zone_code; ?>" />
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ลูกค้า</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="customer" id="customer" maxlength="250" class="form-control input-sm" value="<?php echo $shop->customer_name; ?>"  />
			<input type="hidden" name="customer_code" id="customer_code" value="<?php echo $shop->customer_code; ?>" />
    </div>
  </div>

<?php $btn_yes = $shop->active == 1 ? 'btn-success' : ''; ?>
<?php $btn_no = $shop->active == 0 ? 'btn-danger' : ''; ?>
	<div class="form-group">
 	 <label class="col-sm-3 control-label no-padding-right">เปิดใช้งาน</label>
 	 <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
 		<div class="btn-group width-100">
 			<button type="button" class="btn btn-sm width-50 <?php echo $btn_yes; ?>" id="btn-active-yes" onclick="toggleActive(1)">ใช่</button>
			<button type="button" class="btn btn-sm width-50 <?php echo $btn_no; ?>" id="btn-active-no" onclick="toggleActive(0)">ไม่ใช่</button>
 		</div>
 	 </div>
  </div>

	<input type="hidden" id="active" name="active" value="<?php echo $shop->active; ?>" />

<?php if($this->pm->can_edit) : ?>
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right hidden-xs">&nbsp;</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<button type="button" class="btn btn-sm btn-success pull-right" id="btn-save" onclick="update()"><i class="fa fa-save"></i> Update</button>
    </div>
  </div>
<?php endif; ?>

</form>

<hr class="padding-5 margin-top-20 margin-bottom-20"/>

<div class="row">
	<div class="col-sm-3 hidden-xs">&nbsp;</div>
	<div class="col-md-3 col-sm-5 col-xs-8 padding-5">
		<input type="text" class="form-control input-sm text-center" id="user-box" placeholder="ค้นหาชื่อผู้ใช้งาน" value="" />
	</div>
	<div class="col-sm-1 col-1-harf col-xs-4 padding-5">
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="add_user()"><i class="fa fa-plus"></i> เพิ่มผู้ใช้งาน</button>
	</div>
</div>

<hr class="padding-5 margin-top-20 margin-bottom-20"/>

<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="min-width:800px;">
			<thead>
				<tr>
					<th class="width-5 text-center">#</th>
					<th class="width-30">User Name</th>
					<th class="width-40">Display Name</th>
					<th class="width-15">วันที่เพิ่ม</th>
					<th class="text-center"></th>
				</tr>
			</thead>
			<tbody id="table">
				<?php if(!empty($users)) : ?>
					<?php $no = 1; ?>
					<?php foreach($users as $user) : ?>
						<tr id="row-<?php echo $user->id; ?>">
							<td class="middle text-center no"><?php echo $no; ?></td>
							<td class="middle"><?php echo $user->uname; ?></td>
							<td class="middle"><?php echo $user->name; ?></td>
							<td class="middle"><?php echo thai_date($user->date_add, FALSE, '/'); ?></td>
							<td class="middle text-center">
								<button type="button" class="btn btn-minier btn-danger" onclick="removeUser(<?php echo $user->id; ?>, '<?php echo $user->uname; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							</td>
						</tr>
						<?php $no++; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script id="row-template" type="text/x-handlebarsTemplate">
	<tr id="row-{{id}}">
		<td class="middle text-center no"></td>
		<td class="middle">{{uname}}</td>
		<td class="middle">{{name}}</td>
		<td class="middle">{{date_add}}</td>
		<td class="middle text-center">
			<button type="button" class="btn btn-minier btn-danger" onclick="removeUser({{id}}, '{{uname}}')">
				<i class="fa fa-trash"></i>
			</button>
		</td>
	</tr>
</script>

<script src="<?php echo base_url(); ?>scripts/masters/shop.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
