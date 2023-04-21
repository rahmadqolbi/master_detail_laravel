
<style type="text/css">
	input, select, textarea {
		text-transform: UPPERCASE;
		padding: 5px;
	}
    .select2-container {
  z-index: 1051 !important;
}

.modal {
  z-index: 1052 !important;
}
</style>
<meta name="csrf-token" content="{{ csrf_token() }}" />
<form id="tambah" method="POST">
@csrf
    <table width="100%" cellspacing="0" id="addData">
        <tr>
            <td>
                <label>No.Invoice</label>
            </td>
            <td>
                <input type="text" id="no_invoice3" name="no_invoice" class="FormElement ui-widget-content ui-corner-all autofocus" autofocus autocomplete="off" required value="{{$latihan[0]->no_invoice}}">
            </td>
        </tr>
      
        <tr>
            <td>
                <label>Tanggal Pembelian</label>
            </td>
            <td>
                <input type="text" id="tgl_pembelian3" name="tgl_pembelian" class="FormElement ui-widget-content ui-corner-all setDate" required autocomplete="off" maxlength="30" required value="{{ date('d-m-Y', strtotime($latihan[0]->tgl_pembelian)) }}">
            </td>
        </tr>
		<tr>
            <td>
                <label>Nama Pelanggan</label>
            </td>
            <td>
                <input type="text" id="nama_pelanggan3" name="nama_pelanggan" class="FormElement ui-widget-content ui-corner-all" required autocomplete="off" value="{{$latihan[0]->nama_pelanggan}}">
            </td>
        </tr>
        <tr>
            <td>
                <label>Jenis Kelamin</label>
            </td>
            <td>
                <select id="jenis_kelamin3"  class="jenis_kelamin" name="jenis_kelamin" required style="overflow:hidden;" value="{{$latihan[0]->jenis_kelamin}}">
       
                    <option value="LAKI-LAKI">LAKI-LAKI</option>
                    <option value="PEREMPUAN">PEREMPUAN</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <label>Saldo</label>
            </td>
            <td>
                <input type="text" id="saldo3" name="saldo" class="FormElement ui-widget-content ui-corner-all im-currency" required autocomplete="off" value="{{$latihan[0]->saldo}}">
            </td>
        </tr>
    </table>
    </form>
    
    <br>
    <table width="100%" class="table ui-state-default detail-row" cellpading="5" cellspacing="0" id="detailData">
        <thead>
			<tr>
				<th class="ui-th-div">Nama Barang</th>
				<th class="ui-th-div">Qty</th>
				<th class="ui-th-div">Harga</th>
				<th class="ui-th-div">Action</th>
			</tr>
		</thead>
        <tbody>
        @foreach ($latihan2 as $ll)
            <tr>
                <td>
                    <input type="text" name="nama_barang[]" id="nama_barang" class="FormElement ui-widget-content ui-corner-all" required autocomplete="off" value="{{$ll->nama_barang}}">
                </td>
                <td>
                    <input type="text" name="qty[]" id="qty" class="FormElement ui-widget-content ui-corner-all im-currency" required autocomplete="off" value="{{$ll->qty}}">
                </td>
                <td>
                    <input type="text" name="harga[]" id="harga" class="FormElement ui-widget-content ui-corner-all im-currency" required autocomplete="off" value="{{$ll->harga}}">
                </td>
                <td>
					<a href="javascript:">
						<span class="ui-icon ui-icon-trash" onclick="$(this).parent().parent().parent().remove()"></span>
					</a>
				</td>
            </tr>
            @endforeach
            <tr>
				<td colspan="3"></td>
				<td>
					<a href="javascript:" onclick="addRow(); setNumericFormat();  formBindKeys();">
						<span class="ui-icon ui-icon-plus" ></span>
					</a>
				</td>
			</tr>
        </tbody>
    </table>

<script>
    $(document).ready(function() {
		let index = 0

		setDateFormat()
		setNumericFormat()
        setSelect2()
        formBindKeys()
        autoFocus()
	})

    function autoFocus()
    {
        $('.autofocus').focus();
    }

    function addRow()
    {
        console.log($('#detailData tbody tr'));
        $('#detailData tbody tr').last().before(`
            <tr>
                <td>
                    <input type="text" name="nama_barang[]" id="nama_barang" class="FormElement ui-widget-content ui-corner-all" required autocomplete="off">
                </td>
                <td>
					<input type="text" name="qty[]" id="qty" class="FormElement ui-widget-content ui-corner-all im-currency" required autocomplete="off">
				</td>
				<td>
					<input type="text" name="harga[]" id="harga" class="FormElement ui-widget-content ui-corner-all im-currency" required autocomplete="off">
				</td>
                <td>
					<a href="javascript:">
						<span class="ui-icon ui-icon-trash" onclick="$(this).parent().parent().parent().remove()"></span>
					</a>
				</td>
                
            </tr>
            
        `)
    }

    function setDateFormat() 
    {
        $('.setDate').datepicker({
			dateFormat: 'dd-mm-yy',
		}).inputmask({
			alias: "datetime",
            mask: "1-2-y",
            separator: "-"
		})
    }

    function setSelect2() {
        // console.log('set select2 on add');
  $('.jenis_kelamin').select2({
    dropdownParent: $('.jenis_kelamin').parent(),
  });
}

    function setNumericFormat() {
        $('.im-numeric').keypress(function(e){
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) 
            {
                return false;
            }
        })

        $('.im-currency').inputmask('integer', {
            alias: 'numeric',
            groupSeparator: '.',
            autoGroup: true,
			digitsOptional: false,
			allowMinus: false,
			placeholder: '',
        })
    }
    function  formBindKeys(grid) {
            $(document).on("keydown", function(e) {
                if (activeGrid) {
                    if (
                        e.keyCode == 33 ||
                        e.keyCode == 34 ||
                        e.keyCode == 35 ||
                        e.keyCode == 36 ||
                        e.keyCode == 38 ||
                        e.keyCode == 40 ||
                        e.keyCode == 13
                    ) {
                        e.preventDefault();

                        var gridIds = $(activeGrid).getDataIDs();
                        var selectedRow = $(activeGrid).getGridParam("selrow");
                        var currentPage = $(activeGrid).getGridParam("page");
                        var lastPage = $(activeGrid).getGridParam("lastpage");
                        var currentIndex = 0;
                        var row = $(activeGrid).jqGrid("getGridParam", "postData").rows;

                        for (var i = 0; i < gridIds.length; i++) {
                            if (gridIds[i] == selectedRow) currentIndex = i;
                        }

                        if (triggerClick == false) {
                            if (33 === e.keyCode) {
                                if (currentPage > 1) {
                                    $(activeGrid)
                                        .jqGrid("setGridParam", {
                                            page: parseInt(currentPage) - 1,
                                        })
                                        .trigger("reloadGrid");

                                    triggerClick = true;
                                }
                                $(activeGrid).triggerHandler("jqGridKeyUp"), e.preventDefault();
                            }
                            if (34 === e.keyCode) {
                                if (currentPage !== lastPage) {
                                    $(activeGrid)
                                        .jqGrid("setGridParam", {
                                            page: parseInt(currentPage) + 1,
                                        })
                                        .trigger("reloadGrid");

                                    triggerClick = true;
                                }
                                $(activeGrid).triggerHandler("jqGridKeyUp"), e.preventDefault();
                            }
                            if (35 === e.keyCode) {
                                if (currentPage !== lastPage) {
                                    $(activeGrid)
                                        .jqGrid("setGridParam", {
                                            page: lastPage,
                                        })
                                        .trigger("reloadGrid");
                                    if (e.ctrlKey) {
                                        if (
                                            $(activeGrid).jqGrid("getGridParam", "selrow") !==
                                            $("#customer")
                                            .find(">tbody>tr.jqgrow")
                                            .filter(":last")
                                            .attr("id")
                                        ) {
                                            $(activeGrid)
                                                .jqGrid(
                                                    "setSelection",
                                                    $(activeGrid)
                                                    .find(">tbody>tr.jqgrow")
                                                    .filter(":last")
                                                    .attr("id")
                                                )
                                                .trigger("reloadGrid");
                                        }
                                    }

                                    triggerClick = true;
                                }
                                if (e.ctrlKey) {
                                    if (
                                        $(activeGrid).jqGrid("getGridParam", "selrow") !==
                                        $("#customer")
                                        .find(">tbody>tr.jqgrow")
                                        .filter(":last")
                                        .attr("id")
                                    ) {
                                        $(activeGrid)
                                            .jqGrid(
                                                "setSelection",
                                                $(activeGrid)
                                                .find(">tbody>tr.jqgrow")
                                                .filter(":last")
                                                .attr("id")
                                            )
                                            .trigger("reloadGrid");
                                    }
                                }
                                $(activeGrid).triggerHandler("jqGridKeyUp"), e.preventDefault();
                            }
                            if (36 === e.keyCode) {
                                if (currentPage > 1) {
                                    if (e.ctrlKey) {
                                        if (
                                            $(activeGrid).jqGrid("getGridParam", "selrow") !==
                                            $("#customer")
                                            .find(">tbody>tr.jqgrow")
                                            .filter(":first")
                                            .attr("id")
                                        ) {
                                            $(activeGrid).jqGrid(
                                                "setSelection",
                                                $(activeGrid)
                                                .find(">tbody>tr.jqgrow")
                                                .filter(":first")
                                                .attr("id")
                                            );
                                        }
                                    }
                                    $(activeGrid)
                                        .jqGrid("setGridParam", {
                                            page: 1,
                                        })
                                        .trigger("reloadGrid");

                                    triggerClick = true;
                                }
                                $(activeGrid).triggerHandler("jqGridKeyUp"), e.preventDefault();
                            }
                            if (38 === e.keyCode) {
                                if (currentIndex - 1 >= 0) {
                                    $(activeGrid)
                                        .resetSelection()
                                        .setSelection(gridIds[currentIndex - 1]);
                                }
                            }
                            if (40 === e.keyCode) {
                                if (currentIndex + 1 < gridIds.length) {
                                    $(activeGrid)
                                        .resetSelection()
                                        .setSelection(gridIds[currentIndex + 1]);
                                }
                            }
                            if (13 === e.keyCode) {
                                let rowId = $(activeGrid).getGridParam("selrow");
                                let ondblClickRowHandler = $(activeGrid).jqGrid(
                                    "getGridParam",
                                    "ondblClickRow"
                                );

                                if (ondblClickRowHandler) {
                                    ondblClickRowHandler.call($(activeGrid)[0], rowId);
                                }
                            }
                        }
                    }
                }
            });
        }

   
</script>