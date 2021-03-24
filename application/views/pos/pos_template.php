<script id="row-template" type="text/x-handlebarsTemplate">
<tr id="row-{{id}}">
	<td class="middle" style="padding-left:5px; padding-right:5px;">
		<input type="hidden" class="sell-item" data-id="{{id}}" id="pdCode-{{id}}" value="{{code}}">
		<input type="hidden" id="pdName-{{id}}" value="{{name}}">
		<input type="hidden" id="taxRate-{{id}}" value="{{tax_rate}}">
		<input type="hidden" id="taxAmount-{{id}}" value="{{tax_amount}}">
		<input type="hidden" id="sellPrice-{{id}}" value="{{sell_price}}">
		<input type="hidden" id="discAmount-{{id}}" value="{{discount_amount}}">
		<input type="hidden" id="unitCode-{{id}}" value="{{unit_code}}">
		<input type="text" class="form-control input-xs no-border" value="{{name}} ({{code}})" />
	</td>
	<td class="middle" style="padding-left:5px; padding-right:5px;">
		<input type="number" class="form-control input-xs text-center no-border" id="price-{{id}}" value="{{price}}" onchange="recalItem('{{id}}')" onclick="$(this).select();" />
	</td>
	<td class="middle" style="padding-left:5px; padding-right:5px;">
		<input type="text" class="form-control input-xs text-center input-disc no-border" data-id="{{id}}" id="disc-{{id}}" value="{{discount}}" onchange="recalItem('{{id}}')" onclick="$(this).select();" />
	</td>
	<td class="middle padding-5" style="padding-left:5px; padding-right:5px;">
		<input type="number" class="form-control input-xs text-center input-qty no-border" data-id="{{id}}" id="qty-{{id}}" value="{{qty}}" onchange="recalItem('{{id}}')" onclick="$(this).select();"/>
	</td>
	<td id="total-{{id}}" class="middle text-right row-total" data-id="{{id}}" style="padding-left:5px; padding-right:5px;">{{total}}</td>
	<td class="middle text-center" style="padding-left:5px; padding-right:5px;">
		<span class="pointer" onclick="removeItem('{{id}}')"><i class="fa fa-trash red"></i></span>
	</td>
</tr>
</script>



<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title-site" >ชำระเงิน</h4>
            </div>
            <div class="modal-body">
							<div class="row">
								<div class="col-sm-12 col-xs-12 text-center">
									<span id="payAmountLabel" style="font-size:25px; color:#75ce66;"></span>
									<input type="hidden" id="payableAmount" />
	            	</div>

								<div class="col-sm-12 col-xs-12">
									<label>ชำระโดย</label>
									<select class="form-control input-lg" id="payBy" onchange="changePayment()">
										<?php echo select_pos_payment_method(); ?>
									</select>
								</div>

								<div id="bank_role" class="col-sm-12 col-xs-12 hide">
									<label>เลือกบัญชี</label>
									<select class="form-control input-lg" id="bank_account">
										<?php echo select_bank_account(); ?>
									</select>
								</div>

								<div class="col-sm-12 col-xs-12">
									<label>รับเงิน</label>
									<div class="input-group">
							      <input type="number" class="form-control input-lg text-center" id="receiveAmount" value="" placeholder="รับเงิน">
							      <span class="input-group-btn">
							        <button type="button" class="btn btn-primary btn-lg no-radius payment" onclick="justBalance()">รับพอดี</button>
							      </span>
    							</div>

								</div>

								<div class="col-sm-12 col-xs-12">
									<label class="not-show">Change</label>
									<input type="number" class="form-control input-lg text-center" id="changeAmount" placeholder="เงินทอน" disabled>

								</div>

							</div>
            </div>
            <div class="modal-footer">
               <button class="btn btn-lg btn-info" id="btn-submit" onclick="submitPayment()" disabled>Submit</button>
            </div>
        </div>
    </div>
</div>
