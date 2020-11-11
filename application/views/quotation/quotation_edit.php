<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning hidden" id="btn-leave" onclick="leave()"><i class="fa fa-arrow-left"></i> กลับ</button>
				<button type="button" class="btn btn-sm btn-warning" id="btn-back" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
				<?php if($data->status != 2 && $data->is_closed == 0 &&($this->pm->can_add OR $this->pm->can_edit)) : ?>
					<button type="button" class="btn btn-sm btn-success hidden" id="btn-save" onclick="save()"><i class="fa fa-save"></i> บันทึก</button>
				<?php endif; ?>
				<!--<button type="button" class="btn btn-sm btn-primary" onclick="printQuantation()"><i class="fa fa-print"></i> พิมพ์</button>-->
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>

<div class="row">
  <div class="col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $data->code; ?>" disabled />
  </div>

  <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center edit" name="date_add" id="date_add" value="<?php echo thai_date($data->date_add); ?>" required readonly disabled />
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center edit" name="customer" id="customer" value="<?php echo $data->customer_code; ?>" required disabled />
	</div>
  <div class="col-sm-4 col-xs-6 padding-5">
    <label>ชื่อลูกค้า</label>
    <input type="text" class="form-control input-sm" name="customerName" id="customerName" value="<?php echo $data->customer_name; ?>" disabled/>
  </div>

	<div class="col-sm-3 col-xs-6 padding-5">
    <label>ผู้ติดต่อ</label>
		<input type="text" class="form-control input-sm edit" name="contact" id="contact" value="<?php echo $data->contact; ?>" disabled/>
  </div>

  <div class="col-sm-1 col-xs-6 padding-5">
    <label>เงื่อนไข</label>
		<select class="form-control input-sm edit" name="is_term" id="is_term" disabled>
			<option value="0" <?php echo is_selected($data->is_term, 0); ?>>เงินสด</option>
      <option value="1" <?php echo is_selected($data->is_term, 1); ?>>เครดิต</option>
    </select>
  </div>

	<div class="col-sm-1 col-xs-4 padding-5">
    <label>เครดิต(วัน)</label>
		<input type="number" class="form-control input-sm text-center edit"
		name="credit_term" id="credit_term"
		value="<?php echo $data->credit_term; ?>" <?php echo ($data->is_term == 0 ? 'readonly' : ''); ?>
		disabled/>
  </div>

	<div class="col-sm-1 col-xs-4 padding-5">
    <label>ยืนราคา(วัน)</label>
		<input type="number" class="form-control input-sm text-center edit" name="valid_days" id="valid_days" value="<?php echo intval($data->valid_days); ?>" disabled/>
  </div>

	<div class="col-sm-3 col-xs-4 padding-5">
    <label>ชื่องาน</label>
    <input type="text" class="form-control input-sm edit" name="title" id="title" value="<?php echo $data->title; ?>" disabled>
  </div>

  <div class="col-sm-5 col-xs-8 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $data->remark; ?>" disabled>
  </div>

  <div class="col-sm-1 padding-5 col-xs-4">
    <label class="display-block not-show">Submit</label>
		<?php if($data->status != 2 && $data->is_closed == 0 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
    <button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="get_edit()"><i class="fa fa-pencil"></i> แก้ไข</button>
		<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()"><i class="fa fa-save"></i> บันทึก</button>
		<?php endif; ?>
  </div>
</div>
<input type="hidden" name="customerCode" id="customerCode" value="<?php echo $data->customer_code; ?>" />
<input type="hidden" name="code" id="code" value="<?php echo $data->code; ?>" />

<hr class="margin-top-15 margin-bottom-15 padding-5">

<?php
	if($data->status != 2 && $data->is_closed == 0)
	{
		$this->load->view('quotation/quotation_control');
	}

	$no = 0;
 ?>

<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5">
		<div class="table-responsive">
			<table class="table table-striped border-1">
				<thead>
					<tr>
						<th class="width-5 middle text-center">ลำดับ</th>
						<th class="width-15 middle">รหัสสินค้า</th>
						<th class="middle hidden-xs">ชื่อสินค้า</th>
						<th class="width-8 middle text-right">ราคา</th>
						<th class="width-8 middle text-right">จำนวน</th>
						<th class="width-10 middle text-center">ส่วนลด</th>
						<th class="width-10 middle text-right">มูลค่า</th>
						<th class="width-5"></th>
					</tr>
				</thead>
				<tbody id="detail-table">
			<?php
					$total_qty = 0;
					$total_discount = 0;
					$total_amount = 0;
			?>
			<?php if(!empty($details)) : ?>
			<?php   $no = 1; ?>
			<?php 	foreach($details as $rs) : ?>
				<?php $err = $rs->total_amount < 0 ? 'has-error' : ''; ?>
				<?php $discountLabel = discountLabel($rs->discount1, $rs->discount2, $rs->discount3); ?>

				<tr id="row-<?php echo $rs->id; ?>">
					<td class="middle text-center no"><?php echo $no; ?></td>
					<td class="middle"><?php echo $rs->product_code; ?></td>
					<td class="middle hidden-xs"><?php echo $rs->product_name; ?></td>
					<td class="middle text-right">
						<input type="number"
						class="form-control input-sm text-right row-price edit-row <?php echo $err; ?>"
						id="price-<?php echo $rs->id; ?>"
						data-id="<?php echo $rs->id; ?>"
						data-item="<?php echo $rs->product_code; ?>"
						value="<?php echo $rs->price; ?>" />
					</td>
					<td class="middle text-right">
						<input type="number"
						class="form-control input-sm text-right row-qty edit-row <?php echo $err; ?>"
						id="qty-<?php echo $rs->id; ?>"
						data-id="<?php echo $rs->id; ?>"
						data-item="<?php echo $rs->product_code; ?>"
						value="<?php echo $rs->qty; ?>" />
					</td>
					<td class="middle text-center">
						<input type="text"
						class="form-control input-sm text-center row-disc edit-row <?php echo $err; ?>"
						id="disc-<?php echo $rs->id; ?>"
						data-id="<?php echo $rs->id; ?>"
						data-item="<?php echo $rs->product_code; ?>"
						value="<?php echo $discountLabel; ?>" />
					</td>
					<td class="middle text-right edit-amount <?php echo ($rs->total_amount < 0 ? 'red' : ''); ?>"
					id="amount-<?php echo $rs->id; ?>"
					data-id="<?php echo $rs->id; ?>">
						<?php echo number($rs->total_amount, 2); ?>
					</td>
					<td class="middle text-right">
						<?php if(($data->status == 0 OR $data->status == 1) && $data->is_closed == 0  && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
						<button class="btn btn-minier btn-danger" onclick="removeRow(<?php echo $rs->id; ?>)"><i class="fa fa-trash"></i></button>
					<?php endif; ?>
					</td>
				</tr>
			<?php   $no++; ?>
			<?php   $total_qty += $rs->qty; ?>
			<?php 	$total_discount += $rs->discount_amount; ?>
			<?php 	$total_amount += $rs->total_amount; ?>
			<?php 	endforeach; ?>

			<?php endif; ?>
		</tbody>
		<tfoot>
				<tr>
					<td colspan="5" rowspan="4" style="border-right:solid 1px #cccc;"></td>
					<td class="">จำนวนรวม</td>
					<td class="text-right" id="total-qty"><?php echo number($total_qty); ?></td>
					<td class="text-center">Pcs.</td>
				</tr>
				<tr>
					<td class="">มูลค่ารวม</td>
					<td class="text-right" id="total-amount"><?php echo number($total_amount, 2); ?></td>
					<td class="text-center">THB.</td>
				</tr>
				<tr>
					<td class="">ส่วนลดรวม</td>
					<td class="text-right" id="total-discount"><?php echo number($total_discount, 2); ?></td>
					<td class="text-center">THB.</td>
				</tr>
				<tr>
					<td class="">สุทธิ</td>
					<td class="text-right" id="net-amount"><?php echo number($total_amount - $total_discount, 2); ?></td>
					<td class="text-center">THB.</td>
				</tr>
			</tfoot>
			</table>
		</div>
	</div>
</div>

<input type="hidden" id="no" value="<?php echo $no; ?>" />

<form id="orderForm">
<div class="modal fade" id="orderGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal" style="min-width:250px;">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalTitle">title</h4>
        <div class="margin-top-10 text-center">
          <label>ส่วนลด</label>
          <input type="text" class="form-control input-sm input-medium text-center inline" id="discountLabel" value="0"/>
        </div>
			 </div>
			 <div class="modal-body" id="modalBody"></div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-primary" onClick="insert_item()" >เพิ่มในรายการ</button>
			 </div>
		</div>
	</div>
</div>
</form>


<script id="row-template" type="text/x-handlebarsTemplate">
	<tr id="row-{{id}}">
		<td class="middle text-center no">{{no}}</td>
		<td class="middle">{{product_code}}</td>
		<td class="middle hidden-xs">{{product_name}}</td>
		<td class="middle text-right">
			<input type="number"
			class="form-control input-sm text-right row-price edit-row"
			id="price-{{id}}"
			data-id="{{id}}"
			data-item="{{product_code}}"
			value="{{price}}" />
		</td>
		<td class="middle text-right">
			<input type="number"
			class="form-control input-sm text-right row-qty edit-row?>"
			id="qty-{{id}}"
			data-id="{{id}}"
			data-item="{{product_code}}"
			value="{{qty}}" />
		</td>
		<td class="middle text-center">
			<input type="text"
			class="form-control input-sm text-center row-disc edit-row"
			id="disc-{{id}}"
			data-id="{{id}}"
			data-item="{{product_code}}"
			value="{{discount_label}}" />
		</td>
		<td class="middle text-right edit-amount>"
		id="amount-{{id}}"
		data-id="{{id}}">
			{{amount}}
		</td>
		<td class="middle text-right">
			<button class="btn btn-minier btn-danger" onclick="removeRow({{id}})"><i class="fa fa-trash"></i></button>
		</td>
	</tr>
</script>


<script id="detail-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		{{#if nodata}}
			<tr>
				<td colspan="8" class="middle text-center">---- ไม่พบรายการ ----</td>
			</tr>
		{{else}}
			{{#if subtotal}}
				<tr>
					<td colspan="5" rowspan="4" style="border-right:solid 1px #cccc;"></td>
					<td class="">จำนวนรวม</td>
					<td class="text-right" id="total-qty">{{total_qty}}</td>
					<td class="text-center">Pcs.</td>
				</tr>
				<tr>
					<td class="">มูลค่ารวม</td>
					<td class="text-right" id="total-amount">{{total_amount}}</td>
					<td class="text-center">THB.</td>
				</tr>
				<tr>
					<td class="">ส่วนลดรวม</td>
					<td class="text-right" id="total-discount">{{total_discount}}</td>
					<td class="text-center">THB.</td>
				</tr>
				<tr>
					<td class="">สุทธิ</td>
					<td class="text-right" id="net-amount">{{net_amount}}</td>
					<td class="text-center">THB.</td>
				</tr>
			{{else}}
					<tr id="row-{{id}}">
						<td class="middle text-center">{{no}}</td>
						<td class="middle">{{product_code}}</td>
						<td class="middle hidden-xs">{{product_name}}</td>
						<td class="middle text-right">
							<input type="number"
							class="form-control input-sm text-right row-price edit-row {{err}}"
							id="price-{{id}}"
							data-id="{{id}}"
							data-old="{{price}}"
							value="{{price}}" />
						</td>
						<td class="middle text-right">
							<input type="number"
							class="form-control input-sm text-right row-qty edit-row {{err}}"
							id="qty-{{id}}"
							data-id="{{id}}"
							data-old="{{qty}}"
							value="{{qty}}" />
						</td>
						<td class="middle text-center">
							<input type="text"
							class="form-control input-sm text-center row-disc edit-row {{err}}"
							id="disc-{{id}}"
							data-id="{{id}}"
							data-old="{{discount_label}}"
							value="{{discount_label}}" />
						</td>
						<td class="middle text-right edit-amount {{hilight}}"
						id="amount-{{id}}"
						data-id="{{id}}">
							{{amount}}
						</td>
						<td class="middle text-right">
							{{#if cando}}
							<button class="btn btn-minier btn-danger" onclick="removeRow({{id}})"><i class="fa fa-trash"></i></button>
							{{/if}}
						</td>
					</tr>
			{{/if}}
		{{/if}}
	{{/each}}
</script>




<script src="<?php echo base_url(); ?>scripts/quotation/quotation.js"></script>
<script src="<?php echo base_url(); ?>scripts/quotation/quotation_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_quotation.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/product_tab_menu.js"></script>


<?php $this->load->view('include/footer'); ?>
