<script id="row-template" type="text/x-handlebarsTemplate">
<tr id="row-{{id}}">
	<td class="middle" style="padding-left:5px; padding-right:5px;">
		<input type="hidden" id="pdCode-{{id}}" value="{{code}}">
		<input type="hidden" id="pdName-{{id}}" value="{{name}}">
		<input type="hidden" id="taxRate-{{id}}" value="{{tax_rate}}">
		<input type="hidden" id="taxAmount-{{id}}" value="{{tax_amount}}">
		<input type="hidden" id="sellPrice-{{id}}" value="{{sell_price}}">
		<input type="hidden" id="discAmount-{{id}}" value="{{discount_amount}}">
		{{name}} ({{code}})
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
