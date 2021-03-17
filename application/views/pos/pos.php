<?php $this->load->view('include/pos/pos_header'); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<table class="table" style="margin-bottom: 0px; min-height:90vh;">
			<tr>
				<td class="width-60" style="height:100px; border:0px; padding-left:15px; padding-right:15px; padding-bottom:0px;">
					<form class="form-horizontal" role="form">
						<div class="form-group">
							<div class="col-sm-10 col-xs-8 padding-5">
								<select class="form-control input-sm" id="customer" name="customer">
									<?php if(!empty($customer_list)) : ?>
										<?php foreach($customer_list as $list) : ?>
											<option value="<?php echo $list->code; ?>" <?php echo is_selected($customer_code, $list->code); ?>><?php echo $list->name; ?></option>
										<?php endforeach; ?>
									<?php endif; ?>
								</select>
							</div>
							<div class="col-sm-2 col-xs-4 padding-5">
								<button type="button" class="btn btn-xs btn-primary btn-block" onclick="newCustomer(<?php echo $shop_id; ?>)"><i class="fa fa-plus"></i></button>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-sm-6 col-xs-12 padding-5">
								<input type="text" class="form-control input-sm" name="pd-box" id="pd-box" placeholder="ค้นหาสินค้าด้วย รหัสหรือชื่อสินค้า" />
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12 padding-5">
								<input type="text" class="form-control input-sm" name="barcode-box" id="barcode-box" placeholder="บาร์โค้ดสินค้า"  autofocus>
							</div>
						</div>

					</form>
				</td>

			<?php if(getConfig('USE_PRODUCT_TAB') == 1) : ?>
				<td rowspan="3" class="hidden-xs" style="border:0;">
					<?php
						$this->load->model('masters/product_tab_model');
						$this->load->helper('product_images');
						$this->load->helper('product_tab');

						if(getConfig('PRODUCT_TAB_TYPE') === 'item')
						{
							$this->load->view('pos/pos_item_tab');
						}
						else
						{
							$this->load->view('orders/order_tab_menu');
						}
					?>
				</td>
			<?php endif; ?>
			</tr>

			<tr>
				<td style="border:0px;">
					<table class="table">
						<thead>
							<tr style="background-color:#f9c4be; font-weight:bold;">
								<td class="width-40 text-center">Items</td>
								<td class="width-15 text-center">Price</td>
								<td class="width-15 text-center">Discount</td>
								<td class="width-10 text-center">Qty</td>
								<td class="width-15 text-center">Subtotal</td>
								<td class="width-5 text-center"><i class="fa fa-trash"></i></td>
							</tr>
						</thead>
						<tbody id="item-table">

						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td style="height:100px; border:0;">
					<table class="table" style="margin-bottom:5px;">
						<tr style="background-color:#d9edf7;">
							<td class="width-25">Total Items</td>
							<td class="width-25 text-right" id="total_item">0</td>
							<td class="width-25">Total (After Disc.)</td>
							<td class="width-25 text-right" id="total_amount">0.00</td>
						</tr>
						<tr style="background-color:#d9edf7; color:#3c8dbc;">
							<td class="width-25">Discount</td>
							<td class="width-25 text-right" id="total_discount">0.00</td>
							<td class="width-25">Tax</td>
							<td class="width-25 text-right" id="total_tax">0.00</td>
						</tr>
						<tr style="height:60px; font-size:30px; background-color:black; color:lime;">
							<td colspan="2" class="text-center">Total Payable</td>
							<td colspan="2" class="text-right" id="net_amount">0.00</td>
						</tr>
					</table>

					<table class="table" style="margin-bottom:0px;">
						<tr>
							<td class="width-30" style="padding:0px; border:0;">
								<button type="button" class="btn btn-warning btn-lagrg btn-block">Hold</button>
							</td>
							<td class="width-30" style="padding:0px; border:0;">
								<button type="button" class="btn btn-purple btn-lagrg btn-block">Print Order</button>
							</td>
							<td rowspan="2" class="width-40" style="padding:0px; border:0;">
								<button type="button" class="btn btn-success btn-block" style="height:85px;">Payment</button>
							</td>
						</tr>

						<tr>
							<td class="width-30" style="padding:0px; border:0;">
								<button type="button" class="btn btn-danger btn-lagrg btn-block">Cancel</button>
							</td>
							<td class="width-30" style="padding:0px; border:0;">
								<button type="button" class="btn btn-inverse btn-lagrg btn-block">Print Bill</button>
							</td>

						</tr>

					</table>
				</td>
			</tr>
		</table>
	</div>
</div>

<?php $this->load->view('pos/pos_template'); ?>

<script src="<?php echo base_url(); ?>scripts/order_pos/order_pos.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/pos/pos_footer'); ?>