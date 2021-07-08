<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
		<div class="col-sm-6 padding-5">
			<p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-success" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
				<button type="button" class="btn btn-sm btn-primary" onclick="exportKerryTemplate()"><i class="fa fa-download"></i> Kerry Template</button>
			</p>
		</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-1 col-1-harf padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-sm-1 col-1-harf padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
  </div>

	<div class="col-sm-1 padding-5">
    <label>การชำระเงิน</label>
		<select class="form-control input-sm" name="payment" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_payment_method($payment); ?>
		</select>
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label>ช่องทางขาย</label>
		<select class="form-control input-sm" name="channels" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <?php echo select_channels($channels); ?>
    </select>
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label>การจัดส่ง</label>
		<select class="form-control input-sm" name="sender" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <?php echo select_sender_list($sender); ?>
    </select>
  </div>

	<div class="col-sm-2 padding-5">
    <label>วันที่เอกสาร</label>
    <div class="input-daterange input-group width-100">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>


	<div class="col-sm-1 padding-5">
    <label>สถานะ</label>
    <select class="form-control input-sm" name="print_status" id="printStatus" onchange="getSearch()">
			<option value="0" <?php echo is_selected('0', $print_status); ?>>ยังไม่พิมพ์</option>
			<option value="1" <?php echo is_selected('1', $print_status); ?>>พิมพ์แล้ว</option>
			<option value="all" <?php echo is_selected('all', $print_status); ?> >ทั้งหมด</option>
		</select>
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
  <div class="col-sm-12 padding-5">
    <table class="table table-striped border-1">
      <thead>
        <tr>
					<th class="text-center middle" style="width:20px;">
						<label>
							<input type="checkbox" class="ace" id="chk-all" onchange="checkAll()" />
							<span class="lbl"></span>
						</label>
					</th>
          <th class="width-5 text-center">ลำดับ</th>
          <th class="width-8 text-center">วันที่</th>
          <th class="width-10">เลขที่เอกสาร</th>
          <th class="">ลูกค้า/ผู้รับ/ผู้เบิก</th>
					<th class="width-10">การจัดส่ง</th>
					<th class="width-10">ช่องทางขาย</th>
					<th class="width-10">การชำระเงิน</th>
          <th class="width-10 text-right">ยอดเงิน</th>
          <th class="width-8">พนักงาน</th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($orders))  : ?>
<?php $no = $this->uri->segment(5) + 1; ?>
<?php   foreach($orders as $rs)  : ?>

        <tr class="font-size-12">
					<td class="middle text-center">
						<label>
							<input type="checkbox" class="ace chk" value="<?php echo $rs->code; ?>" />
							<span class="lbl"></span>
						</label>
					</td>
          <td class="text-center">
            <?php echo $no; ?>
          </td>

          <td class="text-center">
            <?php echo thai_date($rs->date_add); ?>
          </td>

          <td class="">
            <?php echo $rs->code; ?>
            <?php //echo ($rs->reference != '' ? ' ['.$rs->reference.']' : ''); ?>
						<?php //if($rs->payment_role == 4 && $rs->is_paid == 0) : ?>
							<!--<span class="label label-danger">รอเงินเข้า</span>-->
						<?php //endif; ?>
          </td>

          <td class="hide-text">
            <?php echo $rs->customer_name; ?>
						<?php if(!empty($rs->customer_ref)) : ?>
							[<?php echo $rs->customer_ref; ?>]
						<?php endif; ?>
          </td>
					<td class="" >
						<?php echo $rs->sender_name; ?>
					</td>
					<td class="" >
						<?php echo $rs->channels_name; ?>
					</td>
					<td class="" >
						<?php echo $rs->payment_name; ?>
					</td>
          <td class="text-right">
            <?php echo number($rs->total_amount,2); ?>
          </td>
          <td class="text-center hide-text">
            <?php echo $rs->user; ?>
          </td>

        </tr>
<?php  $no++; ?>
<?php endforeach; ?>
<?php else : ?>
      <tr>
        <td colspan="10" class="text-center"><h4>ไม่พบรายการ</h4></td>
      </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/report/inventory/delivery_slip.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
