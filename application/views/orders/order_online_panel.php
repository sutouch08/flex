<?php if($order->is_term == 0) : ?>
<?php
        $ship_active = $order->shipping_fee > 0 ? 'disabled' : '';
        $ship_edit = $order->shipping_fee > 0 ? '' : 'hide';
        $ship_update = $order->shipping_fee > 0 ? 'hide' :'' ;
        $service_active = $order->service_fee > 0 ? 'disabled' : '';
        $service_edit = $order->service_fee > 0 ? '' : 'hide';
        $service_update = $order->service_fee > 0 ? 'hide' : '';
?>
<div class="row">
  <div class="col-sm-4 padding-5 first">
  <?php //echo paymentLabel($order->code, paymentExists($order->code), $order->is_paid); ?>
  <?php echo paymentLabel($payments); ?>
  </div>
  <div class="col-sm-8 padding-5 last">
    <p class="pull-right top-p">
      <span class="inline padding-10" style="font-weight:normal;">ค่าจัดส่ง</span>
      <input type="number" class="form-control input-sm input-small inline text-center" id="shippingFee" value="<?php echo $order->shipping_fee; ?>" <?php echo $ship_active;  ?>>
      <?php if($order->is_paid == 0) : ?>
      <button type="button" class="btn btn-xs btn-warning <?php echo $ship_edit; ?>" id="btn-edit-shipping-fee" onclick="activeShippingFee()">แก้ไขค่าจัดส่ง</button>
      <button type="button" class="btn btn-xs btn-success <?php echo $ship_update; ?>" id="btn-update-shipping-fee" onclick="updateShippingFee()">บันทึกค่าจัดส่ง</button>
      <?php endif; ?>

      <label class="inline padding-10" style="margin-left:20px; font-weight:normal;">ค่าบริการ</label>
      <input type="number" class="form-control input-sm input-small inline text-center" id="serviceFee" value="<?php echo $order->service_fee; ?>" <?php echo $service_active; ?>>
      <?php if($order->is_paid == 0) : ?>
      <button type="button" class="btn btn-xs btn-warning <?php echo $service_edit; ?>" id="btn-edit-service-fee" onclick="activeServiceFee()">แก้ไขค่าบริการ</button>
      <button type="button" class="btn btn-xs btn-primary <?php echo $service_update; ?>" id="btn-update-service-fee" onclick="updateServiceFee()">บันทึกค่าบริการ</button>
      <?php endif; ?>
    </p>
  </div>
</div>
<hr />
<div class="row">
  <div class="col-sm-12">
    <div class="tabable">
    	<ul class="nav nav-tabs" role="tablist">
        <li class="active">
        	<a href="#state" aria-expanded="true" aria-controls="state" role="tab" data-toggle="tab">สถานะ</a>
        </li>
  <?php if( $order->is_term == 0 ) : ?>
      	<li role="presentation">
          <a href="#address" aria-expanded="false" aria-controls="address" role="tab" data-toggle="tab">ที่อยู่</a>
        </li>
  <?php endif; ?>
      </ul>
          <!-- Tab panes -->
      <div class="tab-content" style="margin:0px; padding:0px;">
<?php if( $order->is_term == 0 ) : ?>
				<div role="tabpanel" class="tab-pane fade" id="address">
          <div class='row'>
            <div class="col-sm-12">
            <div class="table-responsive">
              <table class='table table-bordered' style="margin-bottom:0px;">
                <thead>
                  <tr>
                    <td colspan="6" align="center">ที่อยู่สำหรับจัดส่ง
                      <p class="pull-right top-p">
                        <button type="button" class="btn btn-info btn-xs" onClick="addNewAddress()"> เพิ่มที่อยู่ใหม่</button>
                      </p>
                    </td>
                  </tr>
                  <tr style="font-size:12px;">
                    <td align="center" width="10%">ชื่อเรียก</td>
                    <td width="12%">ผู้รับ</td>
                    <td width="35%">ที่อยู่</td>
                    <td width="15%">อีเมล์</td>
                    <td width="15%">โทรศัพท์</td>
                    <td ></td>
                  </tr>
                </thead>
                <tbody id="adrs">
          <?php if(!empty($addr)) : ?>
          <?php 	foreach($addr as $rs) : ?>
                  <tr style="font-size:12px;" id="<?php echo $rs->id; ?>">
                    <td align="center"><?php echo $rs->alias; ?></td>
                    <td><?php echo $rs->name; ?></td>
                    <td><?php echo $rs->address.' '. $rs->sub_district.' '.$rs->district.' '.$rs->province.' '. $rs->postcode; ?></td>
                    <td><?php echo $rs->email; ?></td>
                    <td><?php echo $rs->phone; ?></td>
                    <td align="right">
              <?php if( $rs->id == $order->address_id ) : ?>
                      <button type="button" class="btn btn-mini btn-success btn-address" id="btn-<?php echo $rs->id; ?>" onClick="setDefault(<?php echo $rs->id; ?>)">
                        <i class="fa fa-check"></i>
                      </button>
              <?php else : ?>
                      <button type="button" class="btn btn-mini btn-address" id="btn-<?php echo $rs->id; ?>" onClick="setDefault(<?php echo $rs->id; ?>)">
                        <i class="fa fa-check"></i>
                      </button>
              <?php endif; ?>
											<button type="button" class="btn btn-mini btn-primary" onclick="printOnlineAddress(<?php echo $rs->id; ?>, '<?php echo $order->code; ?>')"><i class="fa fa-print"></i></button>
                      <button type="button" class="btn btn-mini btn-warning" onClick="editAddress(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
                      <button type="button" class="btn btn-mini btn-danger" onClick="removeAddress(<?php echo $rs->id; ?>)"><i class="fa fa-trash"></i></button>
                    </td>
                  </tr>
          <?php 	endforeach; ?>
          <?php else : ?>
                  <tr><td colspan="6" align="center">ไม่พบที่อยู่</td></tr>
          <?php endif; ?>
                </tbody>
              </table>
            </div>
            </div>
          </div><!-- /row-->
      </div>
<?php endif; ?>
      <div role="tabpanel" class="tab-pane active" id="state">
<?php $this->load->view('orders/order_state'); ?>
      </div>
    </div>
      </div>
	</div>
</div>
<hr/>
<?php endif; ?>
