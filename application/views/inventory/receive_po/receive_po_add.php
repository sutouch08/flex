<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    	<h3 class="title" >
        <?php echo $this->title; ?>
      </h3>
	</div>
    <div class="col-sm-6">
      	<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
        </p>
    </div>
</div>
<hr />

<form id="addForm" action="<?php echo $this->home.'/add'; ?>" method="post">
<div class="row">
    <div class="col-sm-1 col-1-harf padding-5 first">
    	<label><?php label('doc_num'); ?></label>
        <input type="text" class="form-control input-sm text-center" disabled />
    </div>
		<div class="col-sm-1 padding-5">
    	<label><?php label('date'); ?></label>
      <input type="text" class="form-control input-sm text-center" name="date_add" id="dateAdd" value="<?php echo date('d-m-Y'); ?>" />
    </div>
    <div class="col-sm-8 padding-5">
    	<label><?php label('remark'); ?></label>
        <input type="text" class="form-control input-sm" name="remark" id="remark" />
    </div>
		<div class="col-sm-1 col-1-harf padding-5 last">
			<label class="display-block not-show"><?php label('save'); ?></label>
			<?php 	if($this->pm->can_add) : ?>
							<button type="button" class="btn btn-xs btn-success btn-block" onclick="addNew()"><i class="fa fa-plus"></i> <?php label('add_new'); ?></button>
			<?php	endif; ?>
		</div>
</div>
</form>
<hr class="margin-top-15"/>

<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_add.js"></script>
<?php $this->load->view('include/footer'); ?>
