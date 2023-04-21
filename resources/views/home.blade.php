<!DOCTYPE html>
<html>

<head>
    <meta content="text/html; charset=utf-8" />
    <title>CRUD</title>
    <link rel="stylesheet" type="text/css" media="screen" href="css/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="css/trirand/ui.jqgrid.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/redmond/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css">
    <script src="js/jquery.min.js" type="text/javascript"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="js/trirand/i18n/grid.locale-en.js" type="text/javascript"></script>

    <script src="js/trirand/jquery.jqGrid.min.js" type="text/javascript"></script>
    <!-- from a cdn -->
    <script src="//unpkg.com/autonumeric"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>
    <script src="highlight.js" type="text/javascript"></script>
    <script type="text/javascript" language="javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>

    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>

</head>
<style>
    body {
        font-size: 14px;

    }

    * {
        font-size: 12px;
        text-transform: uppercase;

    }

    * .highlight {
        background-color: #fbec88;
    }
</style>

<body>

    <div class="container">
        <form action="" method="post">
            <table id="grid_id"></table>
            <div id="jqGridPager"></div>
            <div id="penjualan_form"></div>
            <div id="add"></div>
            <div id="edit"></div>
            <div id="del"></div>
            <div id="report_penjualan"></div>
            <div id="export_penjualan"></div>
            <table id="grid_detail"></table>
            <div id="jqGridPagerDetail"></div>
        </form>
    </div>



    <script>
        const customerTable = '#grid_id'
        let indexRow = 0
        let sortName = 'no_invoice'
        let timeout = null
        let highlightSearch
        let no_invoice
        let currentSearch
        let postData
        let ordersPostData
        let triggerClick = true
        let activeGrid = '#grid_id'
        let socket

        const form = document.querySelector('form');
        const table = document.querySelector('table');

        // menambahkan event listener pada form untuk menangkap event submit
        form.addEventListener('submit', function(event) {
            // mencegah perilaku default dari event submit
            event.preventDefault();

            // menetapkan fokus pada elemen pertama pada tabel
            const firstCell = table.querySelector('td');
            if (firstCell) {
                firstCell.focus();
            }
        });
        $(window).resize(function() {
            $(customerTable).setGridWidth($(window).width() - 15)

        })

        $(document).on('click', '#clearFilter', function() {
            currentSearch = undefined
            $('[id*="gs_"]').val('')
            $(customerTable).jqGrid('setGridParam', {
                postData: null
            })
            $(customerTable)
                .jqGrid('setGridParam', {
                    postData: {
                        page: 1,
                        rows: 10,
                        sidx: 'no_invoice',
                        sord: 'asc',
                    },
                })
                .trigger('reloadGrid')
            highlightSearch = 'undefined'
        })
        $('[id*=gs_]').each(function(i, el) {
            $(el).on('focus', function(el) {
                currentSearch = $(this)
            })
        })
        $(document).ready(function() {
            $('#t_grid_id').html(`
		<div id="global_search">
			<label> Global search </label>
			<input  id="gs_global_search" class="ui-widget-content ui-corner-all" style="padding: 4px;" globalsearch="true" clearsearch="true">
		</div>
	`)
        })

        $(document).ready(function() {
            $('#gsh_grid_id_rn').html(`
    <button type="button" id="clearFilter" title="Clear Filter" style="width: 100%; height: 100%;"> X </button>
`).click(function() {
                var grid = $("#jqGridPager");

                // Clear the filter
                if (grid[0] && grid[0].p) {
                    grid.jqGrid('clearGridData');
                    grid[0].p.search = false;
                    $.extend(grid[0].p.postData, {
                        filters: ""
                    });

                    // Reload the grid
                    grid.trigger("reloadGrid");
                }
            })

        })


        $("#grid_id").jqGrid({
            datatype: 'json',
            url: 'http://127.0.0.1:8000/master',
            //NOTE - request data
            // url: 'http://127.0.0.1:8000/master',
            pager: '#jqGridPager',
            emptyrecords: "Nothing to display",
            mtype: 'GET',
            sortable: true,
            editurl: 'update.php',
            colModel: [{
                    name: 'id',
                    label: 'Id',
                    index: 'id',
                    key: true,
                    search: true,
                    sortable: true,
                    datafield: 'id',
                    index: 'id',
                    hidden: true
                },
                {
                    name: 'no_invoice',
                    index: 'no_invoice',
                    label: 'No Invoice',
                    editable: true,

                    search: true,
                    searchoptions: {
                        sopt: ["eq", "ne", "lt", "le", "gt", "ge"]
                    },
                    sortable: true,
                    datafield: 'no_invoice',
                    numberOfColumns: 2,
                    editrules: {
                        edithidden: true,
                        required: true
                    },
                    editoptions: {
                        style: "text-transform: uppercase",
                        dataInit: function(inv) {
                            $(inv).height(8);

                        }
                    },

                },
                {
                    name: 'tgl_pembelian',
                    index: 'tgl_pembelian',
                    label: 'Tanggal Pembelian',
                    sortable: true,
                    editable: true,
                    editoptions: {
                        dataInit: function(element) {
                            $(element).attr('autocomplete', 'off'),
                                $(element).css('text-transform', 'uppercase'),
                                $(element).datepicker({
                                    dateFormat: 'dd-mm-yy'
                                }),
                                $(element).inputmask("date", {
                                    mask: "1-2-y",
                                    separator: "-",
                                    alias: "d-m-y"
                                })
                        }
                    },
                    formatter: 'text',
                    formatoptions: {
                        newformat: 'd-m-Y'
                    },
                    sorttype: 'date',
                    searchoptions: {
                        dataInit: function(element) {
                            $(element).attr('autocomplete', 'off')
                        }
                    }
                },
                {
                    name: 'nama_pelanggan',
                    label: 'Nama Pelanggan',

                    searchoptions: {
                        sopt: ["eq", "ne", "lt", "le", "gt", "ge"]
                    },

                    editable: true,
                    index: 'nama_pelanggan',
                    search: true,
                    sortable: true,
                    editrules: {
                        edithidden: true,
                        required: true
                    },
                    editoptions: {
                        style: "text-transform: uppercase",
                        dataInit: function(inv) {
                            $(inv).height(8);
                        }
                    },
                },
                {
                    name: 'jenis_kelamin',
                    label: 'Jenis Kelamin',
                    index: 'jenis_kelamin',
                    editable: true,
                    search: true,
                    formatter: "select",
                    sortable: true,
                    editrules: {
                        edithidden: true,
                        required: true
                    },
                    edittype: "select",
                    editoptions: {
                        value: "LAKI-LAKI:LAKI-LAKI;PEREMPUAN:PEREMPUAN",
                        dataInit: function(element) {
                            $(element).width(150).select2();
                            $(element).height(5).select2();
                        }
                    },
                },
                {
                    name: 'saldo',
                    idName: 'number',
                    label: 'Saldo',
                    index: 'saldo',
                    editable: true,
                    sorttype: "float",
                    formatter: 'number',
                    searchoptions: {
                        sopt: ["eq", "ne", "lt", "le", "gt", "ge"]
                    },
                    datafield: 'saldo',
                    sortable: true,
                    search: true,
                    align: 'right',
                    editrules: {
                        edithidden: true,
                        required: true
                    },
                    formatoptions: {
                        thousandsSeparator: ".",
                        decimalSeparator: ",",
                        decimalPlaces: 0,
                        defaultValue: '',
                    },
                    editoptions: {
                        dataInit: function(elem) {
                            $(elem).attr('autocomplete', 'off');
                            $(elem).height(8);
                            $(elem).css('text-align', 'right');
                            const autoNumericOptionsEuro = {
                                digitGroupSeparator: '.',
                                decimalCharacter: ',',
                                decimalCharacterAlternative: '.',
                                thousandsSeparator: ".",
                                decimalSeparator: ".",
                                decimalPlaces: 0,
                                currencySymbolPlacement: AutoNumeric.options.currencySymbolPlacement
                                    .suffix,
                                roundingMethod: AutoNumeric.options.roundingMethod.halfUpSymmetric,
                            };
                            new AutoNumeric(elem, autoNumericOptionsEuro);

                        }
                    },


                },


            ],

            viewrecords: true,
            width: 1580,
            height: 200,
            rowNum: 10, //jumlah baris data yang akan ditampilkan pada setiap halaman
            rowList: [10, 20,
                30
            ], //rowList adalah daftar opsi jumlah baris yang dapat dipilih oleh pengguna untuk ditampilkan pada setiap halaman
            // cellEdit: true, //menaktifkan fitur edit langsung
            pager: "#jqGridPager",
            caption: "Master Penjualan",
            editoptions: {
                closeAfterEdit: true,
                closeAfterAdd: true,
                modal: true,

                formId: "form_id"
            },
            sortname: 'no_invoice',
            autoencode: true,
            sortorder: 'asc',
            height: 'auto',
            // loadonce: false,
            rownumbers: true,
            rownumWidth: 40,
            gridview: true,
            search: true,
            // afterSubmit: highlightRow,
            ignoreCase: true,
            shrinkToFit: true,
            autoresizeOnLoad: true,
            toolbar: [true, 'top'],

            onSelectRow: function(id) {

                indexRow = $(this).jqGrid('getCell', id, 'rn') - 1
                page = $(this).jqGrid('getGridParam', 'page') - 1
                rows = $(this).jqGrid('getGridParam', 'postData').rows
                if (indexRow >= rows) indexRow = (indexRow - rows * page)
                reccount = $(this).getGridParam('reccount'); 
                console.log(indexRow)
                console.log(reccount)
                if (reccount == '1') { //NOTE - jika reccount(baris = 1)  
                    indexRow = 9; // baris ke-9 maka indexRow berada posisi ke 9
                    page = page - 1; //NOTE - dan page dikurangi 1 sehingga kembali ke halaman sebelumnya
                
                }
                rowId = $(this).jqGrid('getGridParam', 'selrow')
                cellVal = $(this).jqGrid('getCell', rowId, 'no_invoice')
                row = $(this).jqGrid('getRowData', rowId, )
                no_invoice = row["no_invoice"]
                no_invoice = no_invoice.replace(/<\/?[^>]+(>|$)/g, "");
                $('#grid_detail').jqGrid('setGridParam', {
                    url: '/detail/' + no_invoice + '?global_search=' + ($('#search-input').val())
                }).trigger('reloadGrid');
                // $no_invoice = str_replace('<span class="highlight">', '', $_GET['no_invoice']);
                // $no_invoice = str_replace('</span>', '', $no_invoice);

            },

            loadComplete: function() {

                $(document).unbind('keydown')
                setCustomBindKeys($(this))
                postData = $(this).jqGrid('getGridParam', 'postData')
                var totalPages = $(this).jqGrid('getGridParam', 'lastpage')
                var currentPage = $(this).jqGrid('getGridParam', 'page')
              
                setTimeout(function() {
                    $('#grid_id tbody tr td:not([aria-describedby=grid_id_rn])').highlight(highlightSearch)
                    var lastRowId = $('#grid_id').getDataIDs().length - 1
    if (indexRow > lastRowId) {
        indexRow = lastRowId
    }
    if ($('#grid_id').getDataIDs().length === 0) {
        indexRow = -1 // Reset indexRow if grid is empty
    }
    if (currentPage === totalPages && indexRow > lastRowId) {
        indexRow = lastRowId // Set indexRow to last row on last page
    }

                

                    if (triggerClick) {
                        $('#' + $('#grid_id').getDataIDs()[indexRow]).click()
                        triggerClick = false
                    } else {
                        $('#grid_id').setSelection($('#grid_id').getDataIDs()[indexRow])
                    }
                    $('[id*=gs_]').on('input', function() {
                        highlightSearch = $(this).val()
                        clearTimeout(timeout)

                        timeout = setTimeout(function() {
                            $('#grid_id').trigger('reloadGrid')
                        }, 500);
                    })

                    jQuery("#grid_id").jqGrid({
                        // ... other options ...
                        beforeEditCell: function(rowid, cellname, value, iRow, iCol) {
                            // remove highlighting from search input
                            $('[id*=gs_]').val('')
                        },
                        afterSaveCell: function(rowid, cellname, value, iRow, iCol) {
                            // re-trigger grid reload after editing to refresh highlighting
                            $('#grid_id').trigger('reloadGrid')
                        }
                    });

                    $('#t_grid_id input').on('input', function() {
                        clearTimeout(timeout)
                        timeout = setTimeout(function() {
                            indexRow = 0
                            $(customerTable).jqGrid('setGridParam', {
                                postData: {
                                    'global_search': highlightSearch
                                }
                            }).trigger('reloadGrid')
                        }, 500);
                    })
                    $('input')
                        .css('text-transform', 'uppercase')
                        .attr('autocomplete', 'off')
                }, 500)
            },

        });

        // var source = {
        //     beforeprocessing: function(data) {
        //         source.totalrecords = data[0].TotalRows;
        //     }
        // };

        jQuery("#grid_id").jqGrid('filterToolbar', {
            defaultSearch: "cn",
            searchOnEnter: false, //MENCARI SAAT DI KLIK ENTER FALSE
            searchOperators: true,
            stringResult: true,
            afterSearch: function() {
                indexRow = 0
            },
            gridComplete: function() {
                $("#grid_id").setGridParam({
                    datatype: 'json'
                });

            }
        });

        $("#grid_detail").jqGrid({
            caption: ' Detail Penjualan',
            // url: '/detail2/' + no_invoice,
            // url: 'http://127.0.0.1:8000',
            datatype: 'json',
            height: 'auto',
            pageable: true,
            sortname: sortName,
            rowNum: 10,
            rownumbers: true,
            search: true,
            viewrecords: true,
            autoencode: true,
            gridview: true,
            search: true,
            ignoreCase: true,
            shrinkToFit: true,

            sortable: true,
            pager: '#jqGridPagerDetail',
            colModel: [

                {
                    label: 'Nama Barang',
                    name: 'nama_barang',
                    editrules: {
                        edithidden: true,
                        required: true
                    },
                    index: 'nama_barang',
                    width: 150,
                    align: 'left',
                    search: true

                },
                {
                    label: 'Qty',
                    name: 'qty',
                    index: 'qty',
                    width: 50,
                    editrules: {
                        edithidden: true,
                        required: true
                    },
                    align: 'right',
                    search: true,
                    required: true,
                    formatter: 'currency',
                    formatoptions: {
                        decimalSeparator: ',',
                        thousandsSeparator: '.',
                        decimalPlaces: 0
                    }
                },
                {
                    label: 'Harga',
                    name: 'harga',
                    search: true,
                    index: 'harga',
                    width: 100,
                    editrules: {
                        edithidden: true,
                        required: true
                    },
                    align: 'right',
                    formatter: 'currency',
                    formatoptions: {
                        decimalSeparator: ',',
                        thousandsSeparator: '.',
                        decimalPlaces: 0
                    }

                },
                {
                    name: 'no_invoice',
                    label: 'no_invoice',
                    index: 'no_invoice',
                    key: true,
                    search: true,
                    sortable: true,
                    datafield: 'no_invoice',
                    index: 'no_invoice',
                    hidden: true,

                    // hidden: true,
                },
            ]
        });













        jQuery("#grid_id").jqGrid('navGrid', '#jqGridPager', {
            edit: false, // hide edit button
            add: false, // hide add button
            del: false, // hide delete button
            search: false, // hide search button
            refresh: false, // show refresh button
            // reloadgrid: false,
        });

        jQuery("#grid_id").jqGrid('navGrid', '#jqGridPager', null, {

            recreateForm: true, //formulir akan dibuat ulang setiap kali dialog diaktifkan dengan opsi baru dari colModel
            beforeShowForm: function(form) {
                form[0].querySelector('#no_invoice').setAttribute('readonly', 'readonly')
                var nilaiAsli = "";
                var no_invoice = document.getElementById("no_invoice"); // Ambil element input no_invoice
                var tgl_pembelian = document.getElementById("tgl_pembelian"); // Ambil element input tgl_pembelian
                var nama_pelanggan = document.getElementById("nama_pelanggan"); // Ambil element input nama_pelanggan
                var jenis_kelamin = document.getElementById("jenis_kelamin"); // Ambil element input jenis_kelamin
                var saldo = document.getElementById("saldo"); // Ambil element input saldo

                nilaiAsli = no_invoice.value; // Simpan nilai asli dari input pada variabel nilaiAsli
                var inputBaru = no_invoice.value.replace(/<[^>]+>/g, ""); // Menghapus elemen tag HTML dari input menggunakan regex
                no_invoice.value = inputBaru; // Tampilkan input yang telah diubah

                nilaiAsli = tgl_pembelian.value; // Simpan nilai asli dari input pada variabel nilaiAsli
                var inputBaru = tgl_pembelian.value.replace(/<[^>]+>/g, ""); // Menghapus elemen tag HTML dari input menggunakan regex
                tgl_pembelian.value = inputBaru; // Tampilkan input yang telah diubah

                nilaiAsli = nama_pelanggan.value; // Simpan nilai asli dari input pada variabel nilaiAsli
                var inputBaru = nama_pelanggan.value.replace(/<[^>]+>/g, ""); // Menghapus elemen tag HTML dari input menggunakan regex
                nama_pelanggan.value = inputBaru; // Tampilkan input yang telah diubah

                nilaiAsli = jenis_kelamin.value; // Simpan nilai asli dari input pada variabel nilaiAsli
                var inputBaru = jenis_kelamin.value.replace(/<[^>]+>/g, ""); // Menghapus elemen tag HTML dari input menggunakan regex
                jenis_kelamin.value = inputBaru; // Tampilkan input yang telah diubah

                nilaiAsli = saldo.value; // Simpan nilai asli dari input pada variabel nilaiAsli
                var inputBaru = saldo.value.replace(/<[^>]+>/g, ""); // Menghapus elemen tag HTML dari input menggunakan regex
                saldo.value = inputBaru; // Tampilkan input yang telah diubah




            },
            // recreateForm: true,
            recreateForm: true,
            afterSubmit: callAfterSubmit,
            // reloadAfterSubmit:true,
            closeAfterEdit: true
        }, {
            recreateForm: true,
            afterSubmit: callAfterSubmit,
            closeAfterAdd: true
            // reloadAfterSubmit:true,


        }, );

        function callAfterSubmit(response, postData, oper) {
            var $grid = $(this); // simpan referensi ke grid dalam variabel lokal
            var sortfield = $grid.jqGrid('getGridParam', 'postData').sidx;
            var sortorder = $grid.jqGrid('getGridParam', 'postData').sord;
            var pagesize = $grid.jqGrid('getGridParam', 'postData').rows;
            var filters = $grid.jqGrid('getGridParam', 'postData').filters;
            var global_search = $grid.jqGrid('getGridParam', 'postData').global_search;
            var no_invoice = postData.no_invoice;
            $.ajax({
                url: "add_header.php",
                type: "POST",
                dataType: 'json',
                data: {
                    no_invoice: no_invoice,
                    sidx: sortfield,
                    sord: sortorder,
                    filters: filters,
                    global_search: global_search
                },
                success: function(data) {
                    var totalrows = $grid.getGridParam('records');
                    var position = data.position;
                    var page = Math.ceil(position / pagesize);
                    var row = position - (page - 1) * pagesize;
                    indexRow = row - 1;
                    $grid.jqGrid("setGridParam", {
                        page: page,
                        totalrows: totalrows
                    }).trigger("reloadGrid");
                    $grid.jqGrid("setSelection", row);
                }
            });
        }




        // $('#cData').click();


        // $('#grid_id').navButtonAdd('#jqGridPager', {
        //     caption: "Export To Excel",
        //     title: "Export To Excel",
        //     buttonicon: "ui-icon-document",
        //     onClickButton: function() {
        //         // membuat dialog
        //         $('<div>').appendTo('body').html(`
        //             <div class="ui-state-default" style="padding: 5px;">
        //                 <h5> Tentukan Baris </h5>
        //                 <label> Dari: </label>
        //                 <input type="text" name="start"  value="${$(this).getInd($(this).getGridParam('selrow'))}" class="ui-widget-content ui-corner-all autonumeric" style="padding: 5px; text-transform: uppercase;" max="2" required><br><br>

        //                 <label> Sampai: </label>
        //                 <input type="text" name="limit" value="${$('#grid_id').getGridParam('records')}" class="ui-widget-content ui-corner-all autonumeric" style="padding: 5px; text-transform: uppercase;" max="2" required>
        //             </div>
        //         `).dialog({
        //             title: "Export",
        //             modal: true,
        //             buttons: {
        //                 'Export': function() 
        //                                     {
        //                                         let start = $(this).find('input[name=start]').val()
        //                                         let limit = $(this).find('input[name=limit]').val()
        //                                         let params = ""

        //                                         if (parseInt(start) > parseInt(limit)) {
        //                                             return alert('Sampai harus lebih besar')
        //                                         }

        //                                         let postData = $('#grid_id').jqGrid('getGridParam', 'postData');

        //                                         for (var key in postData) {
        //                                             if (params != "") {
        //                                                 params += "&";
        //                                             }
        //                                             params += key + "=" + encodeURIComponent(postData[key]);
        //                                         }

        //                                         window.open(`export.php?${params}&start=${start}&limit=${limit}&sidx=${postData.sidx}&sord=${postData.sord}&global_search=${postData.global_search}&filters=${postData.filters}`)

        //                                         // window.open(`report.php?start=${start}&limit=${limit}&sidx=${postData.sidx}&sord=${postData.sord}&global_search=${postData.global_search}&filters=${postData.filters}`)

        //                                     },
        //                 "Cancel": function() {
        //                     // tutup dialog
        //                     $(this).dialog("close");
        //                 }
        //             }
        //         });
        //     }
        // });



        $('#grid_id').navButtonAdd('#jqGridPager', {
            caption: "Tambah",
            title: "Tambah Data",
            id: "addPenjualan",
            buttonicon: "ui-icon-plus",
            onClickButton: function() {
                activeGrid = '#grid_id'
                addPenjualan();
            }
        })

        $('#grid_id').navButtonAdd('#jqGridPager', {
            caption: "Edit",
            title: "Edit",
            id: "editPenjualan",
            buttonicon: "ui-icon-pencil",
            onClickButton: function() {
                activeGrid = '#grid_id'
                editPenjualan();
            }
        })



        $('#grid_id').navButtonAdd('#jqGridPager', {
            caption: "Delete",
            title: "Delete",
            id: "deletePenjualan",
            buttonicon: "ui-icon-trash",
            onClickButton: function() {
                activeGrid = '#grid_id'
                deletePenjualan();
            }
        })

        $('#grid_id').navButtonAdd('#jqGridPager', {
            caption: "Report",
            title: "Report",
            buttonicon: "ui-icon-document",
            onClickButton: function() {
                // membuat dialog
                $('<div>').appendTo('body').html(`
                    <div class="ui-state-default" style="padding: 5px;">
                        <h5> Tentukan Baris </h5>
                        <label> Dari: </label>
                        <input type="text" name="start"  value="${$(this).getInd($(this).getGridParam('selrow'))}" class="ui-widget-content ui-corner-all autonumeric" style="padding: 5px; text-transform: uppercase;" max="2" required><br><br>

                        <label> Sampai: </label>
                        <input type="text" name="limit" value="${$('#grid_id').getGridParam('records')}" class="ui-widget-content ui-corner-all autonumeric" style="padding: 5px; text-transform: uppercase;" max="2" required>
                    </div>
                `).dialog({
                    title: "Report",
                    modal: true,
                    buttons: {
                        'Report': function() {
                            let start = $(this).find('input[name=start]').val()
                            let limit = $(this).find('input[name=limit]').val()
                            let params = ""

                            if (parseInt(start) > parseInt(limit)) {
                                return alert('Sampai harus lebih besar')
                            }

                            let postData = $('#grid_id').jqGrid('getGridParam', 'postData');

                            for (var key in postData) {
                                if (params != "") {
                                    params += "&";
                                }
                                params += key + "=" + encodeURIComponent(postData[key]);
                            }

                            window.open('/reports?' + params + '&start=' + start + '&limit=' + limit + '&sidx=' + postData.sidx + '&sord=' + postData.sord + '&global_search=' + postData.global_search + '&filters=' + postData.filters, '_blank');



                            // window.open(`report.php?start=${start}&limit=${limit}&sidx=${postData.sidx}&sord=${postData.sord}&global_search=${postData.global_search}&filters=${postData.filters}`)

                        },
                        "Cancel": function() {
                            // tutup dialog
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });





        // export
        $('#grid_id').navButtonAdd('#jqGridPager', {
            caption: "Export",
            title: "Export",
            id: "penjualanExport",
            buttonicon: "ui-icon-document",
            onClickButton: function() {
                var rowId = $(this).jqGrid('getGridParam', 'selrow');
                var rowData = $(this).jqGrid('getRowData', rowId);
                var no_invoice = rowData['no_invoice'];

                var postData = $(this).jqGrid('getGridParam', 'postData');
                postData['no_invoice'] = no_invoice;
                postData['global_search'] = $('#global_search').val();
                var url = '/export?' + $.param(postData);
                window.open(url);
            }
        });





        // $('#grid_id').navButtonAdd('#jqGridPager', {
        //     caption: "Report",
        //     title: "Report",
        //     id: "reportPenjualan",
        //     buttonicon: "ui-icon-document",
        //     onClickButton: function() {
        //         activeGrid = undefined
        //         reportPenjualan();
        //     }
        // })

        // function reportPenjualan(){
        //     $('#report_penjualan').load('report.php', function() {}).dialog({
        //         modal: true,
        //         title: "Report Penjualan",
        //         height: 'auto',
        //         width: '600',
        //         position: [0,0],
        //     })
        // }




        function addPenjualan() {
            $('#add').load('{{ url("/tambah") }}', function() {}).dialog({

                modal: true,
                title: "Tambah Penjualan",
                height: 'auto',
                width: '600',
                position: [0, 0],
                buttons: {
                    'Simpan': function() {
                        var no_invoice = $('#no_invoice1').val();
                        var tgl_pembelian = $('#tgl_pembelian1').val();
                        var nama_pelanggan = $('#nama_pelanggan1').val();
                        var jenis_kelamin = $('#jenis_kelamin1').val();
                        var saldo = $('#saldo1').val();
                        var penjualan_detail = [];

                        arrayNamaBarang = [];
                        nama_barang = $(`#detailData input[name="nama_barang[]"]`).each(function(index, element) {
                            inputbarang = element.value;
                            arrayNamaBarang.push(element.value);
                        })
                        array_qty = [];
                        qty = $(`#detailData input[name="qty[]"]`).each(function(index, element) {
                            inputqty = element.value;
                            array_qty.push(element.value);
                        })

                        array_harga = [];
                        harga = $(`#detailData input[name="harga[]"]`).each(function(index, element) {
                            inputharga = element.value;
                            array_harga.push(element.value);
                        })
                        if (!no_invoice || !tgl_pembelian || !nama_pelanggan || !jenis_kelamin || !saldo || arrayNamaBarang.length === 0 || array_qty.length === 0 || array_harga.length === 0) {
                            alert('Semua data harus diisi !');
                            return false;
                        }

                        var data_is_empty = false;
                        $(`#detailData input[name="nama_barang[]"], #detailData input[name="qty[]"], #detailData input[name="harga[]"]`).each(function(index, element) {
                            if (element.value == '') {
                                data_is_empty = true;
                            }
                        });

                        if (data_is_empty) {
                            alert('Nama barang, qty, dan harga harus diisi!');
                            return false;
                        }
                        $.ajax({
                            url: '/simpan',
                            type: 'POST',
                            dataType: 'JSON',
                            data: {
                                oper: 'add',
                                no_invoice: no_invoice,
                                tgl_pembelian: tgl_pembelian,
                                nama_pelanggan: nama_pelanggan,
                                jenis_kelamin: jenis_kelamin,
                                saldo: saldo,
                                nama_barang: arrayNamaBarang,
                                qty: array_qty,
                                harga: array_harga
                                // penjualan_detail: penjualan_detail,


                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },

                            success: function(data) {
                                var $grid = $("#grid_id"); // simpan referensi ke grid dalam variabel lokal
                                var sortfield = $grid.jqGrid('getGridParam', 'postData').sidx;
                                var sortorder = $grid.jqGrid('getGridParam', 'postData').sord;
                                var pagesize = $grid.jqGrid('getGridParam', 'postData').rows;
                                var filters = $grid.jqGrid('getGridParam', 'postData').filters;
                                var global_search = $grid.jqGrid('getGridParam', 'postData').global_search;

                                $.ajax({
                                    url: `/getPosition/${no_invoice}`,
                                    type: "POST",
                                    dataType: 'json',
                                    data: {
                                        no_invoice: no_invoice,
                                        sidx: sortfield,
                                        sord: sortorder,
                                        rows: pagesize,
                                        filters: filters,
                                        global_search: global_search
                                    },
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },


                                    success: function(data) {
                                        $('#cData').click();
                                        var totalrows = $grid.getGridParam('records');
                                        var position = data.position;
                                        var page = Math.ceil(position / pagesize);
                                        var row = position - (page - 1) * pagesize;
                                        indexRow = row - 1;
                                        $grid.jqGrid("setGridParam", {
                                            page: page,
                                            totalrows: totalrows
                                        }).trigger("reloadGrid");
                                        $grid.jqGrid("setSelection", row);
                                        console.log(row); //baris
                                        console.log(position); //posisi
                                        console.log(page); //halaman
                                        console.log($grid);

                                        //     iziToast.success({

                                        //     title: 'SUKSES',
                                        //     message: 'Data Berhasil Ditambahkan',
                                        // });
                                    },
                                    error: function(xhr, status, error) {
                                        alert('Terjadi error Kesalahan Saat Menginput Data Pastikan Data Terisi semua');
                                    }
                                });


                            }
                        });
                        $(this).dialog('close')
                    },
                    'Cancel': function() {
                        activeGrid = '#grid_id',
                            $(this).dialog('close')
                    }
                }
            });
        }



        function editPenjualan() {
            $('#edit').load(`{{ url("/edit") }}/${no_invoice}`, function() {}).dialog({

                modal: true,
                title: "Edit Penjualan",
                height: 'auto',
                width: '600',
                position: [0, 0],
                buttons: {
                    'Simpan': function() {
                        var no_invoice = $('#no_invoice2').val();
                        var tgl_pembelian = $('#tgl_pembelian2').val();
                        var nama_pelanggan = $('#nama_pelanggan2').val();
                        var jenis_kelamin = $('#jenis_kelamin2').val();
                        var saldo = $('#saldo2').val();
                        var penjualan_detail = [];

                        arrayNamaBarang = [];
                        nama_barang = $(`#editDetailData input[name="nama_barang[]"]`).each(function(index, element) {
                            inputbarang = element.value;
                            arrayNamaBarang.push(element.value);
                        })
                        array_qty = [];
                        qty = $(`#editDetailData input[name="qty[]"]`).each(function(index, element) {
                            inputqty = element.value;
                            array_qty.push(element.value);
                        })

                        array_harga = [];
                        harga = $(`#editDetailData input[name="harga[]"]`).each(function(index, element) {
                            inputharga = element.value;
                            array_harga.push(element.value);
                        })
                        if (!no_invoice || !tgl_pembelian || !nama_pelanggan || !jenis_kelamin || !saldo || arrayNamaBarang.length === 0 || array_qty.length === 0 || array_harga.length === 0) {
                            alert('Semua data harus diisi !');
                            return false;
                        }

                        var data_is_empty = false;
                        $(`#editDetailData input[name="nama_barang[]"], #editDetailData input[name="qty[]"], #editDetailData input[name="harga[]"]`).each(function(index, element) {
                            if (element.value == '') {
                                data_is_empty = true;
                            }
                        });

                        if (data_is_empty) {
                            alert('Nama barang, qty, dan harga harus diisi!');
                            return false;
                        }
                        console.log("no_invoice value: " + no_invoice);
                        console.log("tgl_pembelian value: " + tgl_pembelian);
                        console.log("nama_pelanggan value: " + nama_pelanggan);
                        console.log("jenis_kelamin value: " + jenis_kelamin);
                        console.log("saldo value: " + saldo);
                        console.log("arrayNamaBarang value: " + arrayNamaBarang);
                        console.log("array_qty value: " + array_qty);
                        console.log("array_harga value: " + array_harga);

                        $.ajax({
                            url: '/simpan_edit',
                            type: 'POST',
                            dataType: 'JSON',
                            data: {
                                oper: 'edit',
                                no_invoice: no_invoice,
                                tgl_pembelian: tgl_pembelian,
                                nama_pelanggan: nama_pelanggan,
                                jenis_kelamin: jenis_kelamin,
                                saldo: saldo,
                                nama_barang: arrayNamaBarang,
                                qty: array_qty,
                                harga: array_harga
                                // penjualan_detail: penjualan_detail,


                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },

                            success: function(data) {
                                var $grid = $("#grid_id"); // simpan referensi ke grid dalam variabel lokal
                                var sortfield = $grid.jqGrid('getGridParam', 'postData').sidx;
                                var sortorder = $grid.jqGrid('getGridParam', 'postData').sord;
                                var pagesize = $grid.jqGrid('getGridParam', 'postData').rows;
                                var filters = $grid.jqGrid('getGridParam', 'postData').filters;
                                var global_search = $grid.jqGrid('getGridParam', 'postData').global_search;

                                $.ajax({
                                    url: `/getPosition/${no_invoice}`,
                                    type: "POST",
                                    dataType: 'json',
                                    data: {
                                        no_invoice: no_invoice,
                                        sidx: sortfield,
                                        sord: sortorder,
                                        rows: pagesize,
                                        filters: filters,
                                        global_search: global_search
                                    },
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },


                                    success: function(data) {
                                        $('#cData').click();
                                        var totalrows = $grid.getGridParam('records');
                                        var position = data.position;
                                        var page = Math.ceil(position / pagesize);
                                        var row = position - (page - 1) * pagesize;
                                        indexRow = row - 1;
                                        $grid.jqGrid("setGridParam", {
                                            page: page,
                                            totalrows: totalrows
                                        }).trigger("reloadGrid");
                                        $grid.jqGrid("setSelection", row);
                                        console.log(row); //baris
                                        console.log(position); //posisi
                                        console.log(page); //halaman
                                        console.log($grid);

                                        //     iziToast.success({

                                        //     title: 'SUKSES',
                                        //     message: 'Data Berhasil Ditambahkan',
                                        // });
                                    },
                                    error: function(xhr, status, error) {
                                        alert('Terjadi error Kesalahan Saat Menginput Data');
                                    }
                                });


                            }
                        });
                        $(this).dialog('close')
                    },
                    'Cancel': function() {
                        activeGrid = '#grid_id',
                            $(this).dialog('close')
                    }
                }
            });
        }

        function deletePenjualan() {
  $('#del').load(`{{ url("/delete") }}/${no_invoice}`, function() {}).dialog({
  }).dialog({
    modal: true,
    title: "Delete Penjualan",
    height: 'auto',
    width: '600',
    position: [0, 0],
    buttons: {
      'Delete': function() {
        $.ajax({
          url: '/proses_delete',
          dataType: 'JSON',
          type: 'POST',
          data: {
            oper: 'del',
            no_invoice: no_invoice,
          },
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(data) {
            $('#grid_id').trigger('reloadGrid'); //refresh grid
            alert('data berhasil dihapus');
            $('#del').dialog('close');
          },
          error: function(xhr, status, error) {
            alert(error);
          }
        });
        $(this).dialog('close');
      },
      'Cancel': function() {
        activeGrid = '#grid_id';
        $(this).dialog('close');
      }
    }
  });
}


        //         function deletePenjualan() {
        //         $('#del').load(`{{ url("/delete") }}/${no_invoice}`, function() {}).dialog({
        //                 modal: true,
        //                 title: "Delete Penjualan",
        //                 height: 'auto',
        //                 width: '600',
        //                 position: [0, 0],
        //                 buttons: {
        //                     'Delete': function() {
        //                         var no_invoice = $('#no_invoice3').val();

        //                         var tgl_pembelian = $('#tgl_pembelian3').val();
        //                         var nama_pelanggan = $('#nama_pelanggan3').val();
        //                         var jenis_kelamin = $('#jenis_kelamin3').val();
        //                         var saldo = $('#saldo3').val();
        //                         var penjualan_detail = [];

        //                         arrayNamaBarang = [];
        //                         nama_barang = $(`#editDetailData input[name="nama_barang[]"]`).each(function(index, element) {
        //                             inputbarang = element.value;
        //                             arrayNamaBarang.push(element.value);
        //                         })
        //                         array_qty = [];
        //                         qty = $(`#editDetailData input[name="qty[]"]`).each(function(index, element) {
        //                             inputqty = element.value;
        //                             array_qty.push(element.value);
        //                         })

        //                         array_harga = [];
        //                         harga = $(`#editDetailData input[name="harga[]"]`).each(function(index, element) {
        //                             inputharga = element.value;
        //                             array_harga.push(element.value);
        //                         })

        //                         $.ajax({
        //                             url: '/proses_delete',
        //                             type: 'POST',
        //                             dataType: 'JSON',
        //                             data: {
        //                                 oper: 'edit',
        //                                 no_invoice: no_invoice,
        //                                 tgl_pembelian: tgl_pembelian,
        //                                 nama_pelanggan: nama_pelanggan,
        //                                 jenis_kelamin: jenis_kelamin,
        //                                 saldo: saldo,
        //                                 nama_barang: arrayNamaBarang,
        //                                 qty: array_qty,
        //                                 harga: array_harga,
        //                             },//data itu kiriman request dari ajaxnya
        //                             headers: {
        //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //     },
        //                             success: function(data) {
        //                                 var $grid = $("#grid_id"); // simpan referensi ke grid dalam variabel lokal
        //                                 var sortfield = $grid.jqGrid('getGridParam', 'postData').sidx;
        //                                 var sortorder = $grid.jqGrid('getGridParam', 'postData').sord;
        //                                 var pagesize = $grid.jqGrid('getGridParam', 'postData').rows;
        //                                 var filters = $grid.jqGrid('getGridParam', 'postData').filters;
        //                                 var global_search = $grid.jqGrid('getGridParam', 'postData').global_search;

        //                                 $.ajax({
        //                                     url: "edit_header.php",
        //                                     // type: "POST",
        //                                     dataType: 'json',
        //                                     data: {
        //                                         no_invoice: no_invoice,
        //                                         sidx: sortfield,
        //                                         sord: sortorder,
        //                                         rows: pagesize,
        //                                         filters: filters,
        //                                         global_search: global_search
        //                                     },
        //                                     success: function(data) {
        //                                         $('#cData').click();
        //                                         var totalrows = $grid.getGridParam('records');
        //                                         var position = data.position;
        //                                         var page = Math.ceil(position / pagesize);
        //                                         var row = position - (page - 1) * pagesize;
        //                                         indexRow = row - 1;
        //                                         $grid.jqGrid("setGridParam", {
        //                                             page: page,
        //                                             totalrows: totalrows
        //                                         }).trigger("reloadGrid");
        //                                         $grid.jqGrid("setSelection", row);
        //                                         $('#grid_id').trigger('reloadGrid'); //refresh halaman
        //                                         iziToast.success({
        //     title: 'SUKSES',
        //     message: 'DATA BERHASIL DI UBAH',
        // });
        //                                     },

        //                                 });
        //                             }
        //                         });
        //                         $(this).dialog('close')
        //                     },
        //                     'Cancel': function() {
        //                         activeGrid = '#grid_id',
        //                             $(this).dialog('close')
        //                     }
        //                 }
        //             });
        //         }

        // function deletePenjualan(){

        // $('#del').load(`deletePenjualan.php?no_invoice=${no_invoice}`, function(){
        // }).dialog({
        //     modal: true,
        //     title: "Delete Penjualan",
        //     height: 'auto',
        //     width: '600',
        //     position: [0,0],
        //     buttons: {
        //         'Delete' : function()
        //         {
        //             var no_invoice = $('#no_invoice').val();
        //             var tgl_pembelian = $('#tgl_pembelian').val();
        //             var nama_pelanggan = $('#nama_pelanggan').val();
        //             var jenis_kelamin = $('#jenis_kelamin').val();
        //             var saldo = $('#saldo').val();
        //             var penjualan_detail = [];

        //                 arrayNamaBarang = [];
        //                 nama_barang = $(`#deleteDetailData input[name="nama_barang[]"]`).each(function(index,element){
        //                     inputbarang = element.value;
        //                     arrayNamaBarang.push(element.value);
        //                 })
        //             array_qty = [];
        //             qty = $(`input[name="qty[]"]`).each(function(index,element){
        //                 inputqty = element.value;
        //                 array_qty.push(element.value);
        //             })

        //             array_harga = [];
        //             harga = $(`input[name="harga[]"]`).each(function(index,element){
        //                 inputharga = element.value;
        //                 array_harga.push(element.value);
        //             })
        //             $.ajax({
        //                 url: 'update.php',
        //                 type: 'POST',
        //                 dataType: 'JSON',
        //                 data:{
        //                     oper: 'del',
        //                     no_invoice : no_invoice,
        //                     tgl_pembelian : tgl_pembelian,
        //                     nama_pelanggan : nama_pelanggan,
        //                     jenis_kelamin : jenis_kelamin,
        //                     saldo : saldo,
        //                     nama_barang: arrayNamaBarang,
        //                     qty: array_qty,
        //                     harga: array_harga,
        //                 },
        //                 success: function(data){
        //                     alert('berhasil');
        //                     $('#grid_id').trigger('reloadGrid'); //refresh halaman
        //                 }
        //             });
        //             $(this).dialog('close');
        //         },
        //         'Cancel': function(){
        //             activeGrid = '#grid_id',
        //             $(this).dialog('close')
        //         }
        //     }
        // });
        // }






        function setCustomBindKeys(grid) {
            $(document).on("keydown", function(e) {
              
                if (activeGrid) {
                    console.log(activeGrid);
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

</body>

</html>