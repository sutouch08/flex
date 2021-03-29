<?php $this->load->view('include/pos/pos_header'); ?>
<div class="row margin-top-30">
	<div class="col-sm-12 col-xs-12 text-center">
		<button type="button" class="btn btn-lg btn-primary" onclick="goAdd(<?php echo $pos_id; ?>)">ขายสินค้า</button>
		<button type="button" class="btn btn-lg btn-info" onclick="showHoldBill(<?php echo $pos_id; ?>)">
			บิลที่พักไว้
			<?php if($hold_bills > 0) : ?>
				<span class="badge"><?php echo $hold_bills; ?></span>
			<?php endif; ?>
			</button>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/order_pos/order_pos.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/pos/pos_footer'); ?>
