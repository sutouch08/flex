<?php $this->load->view('include/header'); ?>
<?php $pm = get_permission('SOODIV', $this->_user->uid, get_cookie('id_profile')); ?>
<?php $use_vat = getConfig('USE_VAT'); ?>
<script>
	var USE_VAT = <?php echo $use_vat; ?>
</script>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
		<div class="col-sm-6 col-xs-6 padding-5">
			<p class="pull-right top-p">
				<?php if($pm->can_add OR $pm->can_edit) : ?>
					<?php $inv_option = $use_vat ? 'tax_invoice' : 'do_invoice'; ?>
					<button type="button" class="btn btn-sm btn-primary" onclick="create_each_invoice('<?php echo $inv_option; ?>')">เปิดใบกำกับแยกออเดอร์</button>
					<button type="button" class="btn btn-sm btn-success" onclick="create_one_invoice('<?php echo $inv_option; ?>')">เปิดใบกำกับรวมออเดอร์</button>
				<?php endif; ?>
			</p>
		</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>ใบกำกับ</label>
    <input type="text" class="form-control input-sm search" name="invoice_code"  value="<?php echo $invoice_code; ?>" />
  </div>

  <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label>การชำระเงิน</label>
		<select class="form-control input-sm" name="payment" onchange="getSearch()">
			<option value="">ทั้งหมด</option>
			<?php echo select_payment_method($payment); ?>
		</select>
  </div>

	<!--
	<div class="col-sm-1 col-1-harf padding-5">
    <label>รูปแบบ</label>
		<select class="form-control input-sm" name="role" onchange="getSearch()">
      <option value="">ทั้งหมด</option>
      <?php echo select_order_role($role); ?>
    </select>
  </div>
-->
	<div class="col-sm-1 col-1-harf padding-5">
    <label>ช่องทางขาย</label>
		<select class="form-control input-sm" name="channels" onchange="getSearch()">
      <option value="">ทั้งหมด</option>
      <?php echo select_channels($channels); ?>
    </select>
  </div>
	<div class="col-sm-2 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>

  </div>

  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-sm-12">
    <table class="table table-striped border-1">
      <thead>
        <tr>
					<th class="width-5 text-center"></th>
          <th class="width-5 text-center">ลำดับ</th>
          <th class="width-8 text-center">วันที่</th>
          <th class="width-15">เลขที่เอกสาร</th>
					<th class="width-10">ใบกำกับ</th>
          <th class="">ลูกค้า/ผู้รับ/ผู้เบิก</th>
          <th class="width-10 text-center">ยอดเงิน</th>
          <th class="width-10 text-center">การชำระเงิน</th>
          <th class="width-15 text-center">พนักงาน</th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($orders))  : ?>
<?php $no = $this->uri->segment(4) + 1; ?>
<?php   foreach($orders as $rs)  : ?>

        <tr class="font-size-12">
					<td class="text-center">
						<?php if($rs->role === 'S' && empty($rs->invoice_code)) : ?>
							<label>
								<input type="checkbox" class="ace chk" value="<?php echo $rs->code; ?>" data-no="<?php echo $no; ?>" />
								<span class="lbl"></span>
							</label>
						<?php endif; ?>
					</td>
          <td class="text-center pointer" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php echo $no; ?>
          </td>

          <td class="pointer text-center" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php echo thai_date($rs->date_add); ?>
          </td>

          <td class="pointer" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php echo $rs->code; ?>
            <?php echo ($rs->reference != '' ? ' ['.$rs->reference.']' : ''); ?>
						<?php if($rs->payment_role == 4 && $rs->is_paid == 0) : ?>
							<span class="label label-danger">รอเงินเข้า</span>
						<?php endif; ?>
						<input type="hidden" id="orderCode-<?php echo $no; ?>" value="<?php echo $rs->code; ?>" />
          </td>

					<td class="pointer text-center" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php echo $rs->invoice_code; ?>
          </td>

          <td class="pointer hide-text" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php echo $rs->customer_name; ?>
						<?php if(!empty($rs->customer_ref)) : ?>
							[<?php echo $rs->customer_ref; ?>]
						<?php endif; ?>
						<input type="hidden" id="customerCode-<?php echo $no; ?>" value="<?php echo $rs->customer_code; ?>" />
          </td>

          <td class="pointer text-center" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php echo number($rs->total_amount,2); ?>
          </td>

          <td class="pointer text-center" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php echo $rs->payment_name; ?>
          </td>

          <td class="pointer text-center hide-text" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php echo $rs->user; ?>
          </td>

        </tr>
<?php  $no++; ?>
<?php endforeach; ?>
<?php else : ?>
      <tr>
        <td colspan="8" class="text-center"><h4>ไม่พบรายการ</h4></td>
      </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/order_closed/closed.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/order_closed/closed_list.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
