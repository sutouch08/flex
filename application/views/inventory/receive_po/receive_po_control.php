
<div class="row">
	<div class="col-sm-10">
		<?php if(empty($doc->po_code)) : ?>
		<div class="row">
			<div class="col-sm-2 padding-5 first">
		    <label>รุ่นสินค้า</label>
		    <input type="text" class="form-control input-sm text-center" name="pdCode" id="pd-code" value="" autofocus>
		  </div>
		  <div class="col-sm-1 padding-5">
		    <label class="display-block not-show">search</label>
		    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getProductGrid()">ดึงรายการ</button>
		  </div>

			<div class="col-sm-3 col-sm-offset-2 padding-5">
				<label>รหัสสินค้า</label>
				<input type="text" class="form-control input-sm text-center" name="itemCode" id="item-code" value=""/>
			</div>
			<div class="col-sm-1 padding-5">
				<label>จำนวน</label>
				<input type="number" class="form-control input-sm text-center" name="itemQty" id="item-qty" value=""/>
			</div>
			<div class="col-sm-1 padding-5">
				<label class="display-block not-show">get</label>
				<button type="button" class="btn btn-xs btn-primary btn-block" id="btn-add-item" onclick="addItem()">เพิ่ม</button>
			</div>
		</div>
		<?php endif; ?>
	</div>


  <div class="col-sm-1 padding-5">
    <?php if(!empty($doc->po_code)) : ?>
    <label class="display-block not-show">getPo</label>
    <button type="button" class="btn btn-xs btn-info btn-block" onclick="getData()">ดึงใบสั่งซื้อ</button>
    <?php endif; ?>
  </div>
  <div class="col-sm-1 padding-5 last">
    <label class="display-block not-show">delete</label>
    <button type="button" class="btn btn-xs btn-danger btn-block" onclick="removeAll()">ลบทั้งหมด</button>
  </div>
</div>
<hr class="margin-top-15">


<form id="orderForm">
<div class="modal fade" id="orderGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalTitle">title</h4>
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


<div class="modal fade" id="poGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:800px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <center style="margin-bottom:10px;"><h4 class="modal-title" id="po-title">title</h4></center>
      </div>
      <div class="modal-body">
        <table class="table table-striped table-bordered">
          <thead>
            <th class="width-10 text-center">#</th>
            <th class="width-20 text-center">รหัส</th>
            <th class="text-center">สินค้า</th>
            <th class="width-10 text-center">ราคา</th>
            <th class="width-10 text-center">ค้างรับ</th>
            <th class="width-10 text-center">จำนวน</th>
          </thead>
          <tbody id="po-body">

          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="btn_close" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-yellow" onclick="receiveAll()">รับยอดค้างทั้งหมด</button>
				<button type="button" class="btn btn-purple" onclick="clearAll()">เคลียร์ตัวเลขทั้งหมด</button>
        <button type="button" class="btn btn-primary" onclick="insertPoItems()">เพิ่มในรายการ</button>
       </div>
    </div>
  </div>
</div>



<script id="receiveTableTemplate" type="text/x-handlebarsTemplate">
	{{#each this}}
		{{#if nodata}}
		<tr>
			<td colspan="7" class="middle text-center">---- ไม่พบรายการ ----</td>
		</tr>
		{{else}}
			{{#if @last}}
				<tr>
				<td colspan="4" class="middle text-right"><strong>รวม</strong></td>
				<td class="middle text-right"><strong>{{total_qty}}</strong></td>
				<td class="middle text-right"><strong>{{total_amount}}</strong></td>
				<td></td>
				</tr>
			{{else}}
					<tr>
						<td class="middle text-center no">{{no}}</td>
						<td class="moddle">{{product_code}}</td>
						<td class="middle">{{product_name}}</td>
						<td class="middle text-right">{{price}}</td>
						<td class="middle text-right">{{qty}}</td>
						<td class="middle text-right">{{amount}}</td>
						<td class="middle text-center">
							{{#if open}}
								<button type="button" class="btn btn-minier btn-danger" onclick="removeRow({{id}}, '{{product_code}}')">
									<i class="fa fa-trash"></i>
								</button>
							{{/if}}
						</td>
					</tr>
			{{/if}}
		{{/if}}
	{{/each}}
</script>

<script id="row-template" type="text/x-handlebarsTemplate">
{{#each this}}
<tr>
  <td class="text-center middle no">{{no}}</td>
  <td class="middle">{{pdCode}}</td>
  <td class="middle">{{pdName}}</td>
  <td class="middle text-center">
    <input type="number" class="form-control input-sm text-center receive-box" id="receive-{{id_pa}}" value="{{qty}}" />
    <span class="hide" id="label-{{id_pa}}">{{qty}}</span>
    <input type="hidden" id="productId-{{id_pa}}" value="{{id_pd}}" />
  </td>
  <td class="middle text-center">
    <button type="button" class="btn btn-sm btn-danger" id="btn-remove-{{id_pa}}" onclick="deleteRow({{id_pa}})"><i class="fa fa-trash"></i></button>
  </td>
</tr>
{{/each}}
</script>


<script id="po-template" type="text/x-handlebarsTemplate">
{{#each this}}
<tr class="item-row">
  <td class="text-center middle no">{{no}}</td>
  <td class="middle">{{pdCode}}</td>
  <td class="middle">{{pdName}}</td>
  <td class="middle text-center">{{price}}</td>
  <td class="middle text-center" id="backlogs-{{no}}">{{backlogs}}</td>
  <td class="middle text-center">
    <input type="number" class="form-control input-sm text-center receive_qty" data-no="{{no}}" data-pdcode="{{pdCode}}" id="pdCode-{{no}}" value="{{qty}}" />
		<input type="hidden" id="qty-{{no}}" value="{{qty}}" />
  </td>
</tr>
{{/each}}
</script>
