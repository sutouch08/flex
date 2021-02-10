<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-4 padding-5">
    <h3 class="title">
			<i class="fa fa-file-text-o"></i> 
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6 col-xs-8 padding-5">
    	<p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
				<?php if($order->status != 2 && $this->pm->can_delete) : ?>
					<button type="button" class="btn btn-sm btn-danger" onclick="getDelete()"><i class="fa fa-times"></i> ยกเลิก</button>
				<?php endif; ?>
				<?php if($order->status == 1) : ?>

					<?php if($use_vat) : ?>
						<button type="button" class="btn btn-sm btn-info" onclick="print_tax_receipt()"><i class="fa fa-print"></i> พิมพ์ใบเสร็จรับเงิน</button>
						<button type="button" class="btn btn-sm btn-info" onclick="print_tax_invoice()"><i class="fa fa-print"></i> พิมพ์ใบแจ้งหนี้</button>
					<?php else : ?>
						<button type="button" class="btn btn-sm btn-info" onclick="print_do_invoice()"><i class="fa fa-print"></i> พิมพ์ใบแจ้งหนี้</button>
						<button type="button" class="btn btn-sm btn-info" onclick="print_do_receipt()"><i class="fa fa-print"></i> พิมพ์ใบเสร็จ</button>
					<?php endif; ?>
				<?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5">
<div class="row">
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label for="code">เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" id="code" name="code" value="<?php echo $order->code; ?>" disabled />
	</div>
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label for="doc_date">วันที่</label>
		<input type="text" class="form-control input-sm text-center edit" name="doc_date" id="doc_date" value="<?php echo thai_date($order->doc_date); ?>" readonly disabled />
	</div>
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label for="customer_code">รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center edit" name="customer_code" id="customer_code" value="<?php echo $order->customer_code; ?>" disabled />
	</div>
	<?php if($use_vat) : ?>
	<div class="col-sm-6 col-xs-6 padding-5">
		<label for="customer_name">ลูกค้า</label>
		<input type="text" class="form-control input-sm" name="customer_name" id="customer_name" value="<?php echo $order->customer_name; ?>" disabled />
	</div>
	<div class="col-sm-1 col-1-harf col-xs-12 padding-5">
		<label for="phone">ราคาขาย</label>
		<select class="form-control input-sm edit" id="vat_type" name="vat_type" disabled>
			<option value="I" <?php echo is_selected('I', $order->vat_type); ?>>รวม VAT</option>
			<option value="E" <?php echo is_selected('E', $order->vat_type); ?>>ไม่รวม VAT</option>
		</select>
	</div>
	<?php else : ?>
	<div class="col-sm-7 col-7-harf col-xs-6 padding-5">
		<label for="customer_name">ลูกค้า</label>
		<input type="text" class="form-control input-sm" name="customer_name" id="customer_name" value="<?php echo $order->customer_name; ?>" disabled />
	</div>
	<?php endif; ?>
	<div class="col-sm-2 col-xs-6 padding-5">
		<label for="branch_name">สาขา</label>
		<input type="text" class="form-control input-sm text-center" name="branch_name" id="branch_name" value="<?php echo $order->branch_name; ?>" disabled />
	</div>
	<div class="col-sm-10 col-xs-12 padding-5">
		<label for="address">ที่อยู่</label>
		<input type="text"
			class="form-control input-sm"
			name="address"
			id="address"
			value="<?php echo $address; ?>" disabled />
	</div>

</div>
<input type="hidden" id="customerCode" value="<?php echo $order->customer_code; ?>" />
<?php
if($order->status == 2)
{
  $this->load->view('cancle_watermark');
}
?>
<hr class="padding-5" />
<div class="row">
<div class="col-sm-12 col-xs-12 padding-5">
	<?php if(!empty($reference)) : ?>
		<span>อ้างอิง : </span>
		<?php foreach($reference as $ref) : ?>
			<span class="label label-info label-white label-xlg middle">
				<?php echo $ref->order_code; ?>
			</span>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
</div>
<hr class="padding-5 margin-top-15" />
<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table border-1">
			<thead>
				<tr>
					<th class="width-5 text-center">#</th>
					<th class="width-10">รหัส</th>
					<th class="width-25">รายละเอียด</th>
					<th class="width-10">อ้างอิง</th>
					<th class="width-8 text-right">จำนวน</th>
					<th class="width-8">หน่วยนับ</th>
					<th class="width-8 text-right">ราคา</th>
					<th class="width-10 text-right">ส่วนลด</th>
					<th class="width-15 text-right">จำนวนเงิน</th>
				</tr>
			</thead>
			<tbody>
	<?php $no = 1; ?>
	<?php $total_amount_ex = 0; ?>
	<?php $total_amount_inc = 0; ?>
	<?php $total_vat = 0; ?>
	<?php if(!empty($details)) : ?>
		<?php foreach($details as $rs) : ?>
				<?php $price = vat_price($rs->price, $order->vat_type, $rs->vat_rate); //--- vat_helper ?>
				<?php $amount = vat_price($rs->amount, $order->vat_type, $rs->vat_rate); ?>

				<tr id="row-<?php echo $rs->id; ?>">
					<td class="middle text-center no"><?php echo $no; ?></td>
					<td class="middle"><?php echo $rs->product_code; ?></td>
					<td class="middle"><?php echo $rs->product_name; ?></td>
					<td class="middle"><?php echo $rs->order_code; ?></td>
					<td class="middle text-right"><?php echo number($rs->qty,2); ?></td>
					<td class="middle"><?php echo $rs->unit_name; ?></td>
					<td class="middle text-right"><?php echo number($price, 2); ?></td>
					<td class="middle text-right"><?php echo $rs->discount_label; ?></td>
					<td class="middle text-right"><?php echo number($amount, 2); ?></td>
				</tr>
			<?php $no++; ?>
			<?php $total_amount_inc += $rs->amount; ?>
			<?php $total_amount_ex += $amount; ?>
			<?php $total_vat += $rs->vat_amount; ?>
		<?php endforeach; ?>
	<?php else : ?>
		<tr>
			<td colspan="9" class="text-center">&nbsp;</td>
		</tr>
	<?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="7" rowspan="<?php echo ($use_vat === TRUE ? 3 : 2); ?>" style="white-space:normal;">
						<b>หมายเหตุ : </b>
						<?php echo $order->remark; ?>
					</td>
					<td class="middle text-right">รวมเป็นเงิน</td>
					<td class="middle text-right"><?php echo number($total_amount_ex, 2); ?></td>
				</tr>
			<?php if($use_vat) : ?>
				<tr>
					<td class="middle text-right">ภาษีมูลค่าเพิ่ม <?php echo getConfig('SALE_VAT_RATE'); ?> %</td>
					<td class="middle text-right"><?php echo number($total_vat, 2); ?></td>
				</tr>
			<?php endif; ?>
				<tr>
					<td class="middle text-right">จำนวนเงินรวมทั้งสิ้น</td>
					<td class="middle text-right"><?php echo number($total_amount_inc, 2); ?></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>



<div class="modal fade" id="order-list-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:300px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body" >
            <div id="orderList"></div>
            </div>
            <div class="modal-footer">
               <button class="btn btn-sm btn-info btn-block" data-dismiss="modal" onclick="addToOrder()">เพิ่มรายการที่เลือก</button>
            </div>
        </div>
    </div>
</div>

<script type="text/x-handlebarsTemplate" id="orderListTemplate">
	<table class="table table-striped" style="margin-bottom:0px;">
	{{#each this}}
		<tr>
			<td class="width-50">
			<label>
				<input type="checkbox" class="ace chk" value="{{orderCode}}">
				<span class="lbl">&nbsp;&nbsp; {{orderCode}}</span>
			</label>
			</td>
			<td class="width-50 text-right">{{amount}}</td>

		</tr>
	{{/each}}
	</table>
</script>





<script src="<?php echo base_url(); ?>scripts/order_invoice/order_invoice.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/order_invoice/order_invoice_control.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
