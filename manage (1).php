<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap4-toggle/3.6.1/bootstrap4-toggle.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowgroup/1.1.2/css/rowGroup.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css">

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s mbot10" style="margin-bottom: -6px;">
                    <div class="panel-body _buttons">
                    <div class="col-md-6">
                           <h4 class="bold mnthly_return_heading">Monthly Employee Return</h4>  
                          
                        </div>
                        <div class="col-md-6 text-right">
                          
                           <a href="<?= admin_url('employee_return/employee_return') ?>" class="btn btn-primary save_btn">Add EMP 201</a>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row filter_section">
                        <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="client_name">Select Client Name</label>
                                        <select id="client_name" class="form-control">
                                            <option value="">All Clients</option>
                                            <?php foreach ($clients as $client): ?>
                                                <option value="<?php echo $client->code; ?>"><?php echo $client->account; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="financial_year">Select Financial Year</label>
                                            <select id="financial_year" class="form-control">
                                                <option value="">All Years</option>
                                                <?php foreach ($financial_years as $year): ?>
                                                    <option value="<?php echo $year->year; ?>"><?php echo $year->year; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                 </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="filter_status">Select Status</label>
                                        <select id="filter_status" class="form-control">
                                            <option value="">All</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                        </div>
                </div>

                <div class="panel_s mbot10" style="margin-bottom: -6px;">
                    <div class="panel-body _buttons">
                        <table id="emp_201" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr style="text-align:center;">
                                    <th></th>
                                    
                                    <th >YEAR END</th>
                                    <th>CLIENT CODE</th>
                                    <th style="width:150px">REFERENCES</th>
                                    <th>TAX PERIOD</th>
                                    <th style="width:120px">PAY</th>
                                    <th style="width:120px">SDL</th>
                                    <th style="width:120px">UIF</th>
                                    <th  style="width:120px">LIABILITY</th>
                                    <th>PENELTIES</th>
                                    <th>TOTAL DUE</th>
                                    <th>AMOUNT PAID</th>
                                    <th>DATE PAID</th>
                                    <th>OUT STANDING</th>
                                    <th>COMPLAINT</th>
                                    <th>Status</th>
                                    <th  >Date</th>
                                    <th>Action</th>
                                    
                                    </tr>
                                
                                </thead>
                                <tbody>
                                    <?php foreach($emp_data as $row): ?>

                                        <?php
                                        $totalLiability = $row->pay_liability + $row->sdl_liability + $row->uif_liability;
                                        $totalPenalty = $row->penalty + $row->interest + $row->other;
                                        $totalDue = $totalLiability + $totalPenalty;

                                    
                                        ?>
                                        <tr>
                                        <td>
                                            <a href="#" class="toggle-row" data-id="<?= $row->id; ?>">
                                            <i class="fa fa-plus-circle text-success fa-2x"></i>
                                            </a>
                                            </td>
                                            
                                            <td class="text-center"><?= @$row->financial_year;?></td>
                                            <td><?= @$row->client_code;?></td>
                                            <td ><?= $row->payment_reference;?></td>
                                            <td><?= $row->pay_period;?></td>
                                            <td class="right_align"><?= format_number($row->pay_liability);?></td>
                                            <td class="right_align"><?= format_number($row->sdl_liability);?></td>
                                            <td class="right_align"><?= format_number($row->uif_liability);?></td>
                                            <td class="right_align"><?= format_number($totalLiability);?></td>
                                            <td class="right_align"><?= format_number($totalPenalty);?> </td>
                                            <td class="right_align"><?= format_number($totalDue);?></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td><?= $row->active;?></td>
                                            <td ><?= date('d-m-Y', strtotime($row->created_at)); ?></td>
                                            <td width="100">
                                            <div class="btn-group" style="display: flex;">
                                                    <a  href="<?= admin_url('employee_return/view_file/' . $row->id) ?>" class="btn btn-secondary-light btn-icon waves-effect waves-themed has-tooltip action_hover_button" style="margin-right: 10px; font-size: 18px;background-color: #d7d9dc !important;"><i class="fa-solid fa-eye" aria-hidden="true"></i></a>
                                                    
                                                    <a  href="<?= admin_url('employee_return/employee_return/'.$row->id) ?>" class="btn btn-sm" style="margin-right: 10px; font-size: 18px;background-color: #d7d9dc !important;"><i class="fa fa-pencil"></i></a>
                                                    
                                                    <a data-link="<?= admin_url('employee_return/employee_return/'.$row->id) ?>" href="javascript:void(0)" class="email_document btn btn-secondary-light btn-icon waves-effect waves-themed has-tooltip action_hover_button" style="margin-right: 10px; font-size: 18px;background-color: #d7d9dc !important;"><i class="fa-solid fa-envelope" aria-hidden="true"></i></a>
                                                    <a href="https://wa.me/27615429391?text=<?= urlencode(base_url('uploads/emp201/pdf/' . @$row->emp_201_file)) ?>" class="btn btn-secondary-light btn-icon waves-effect waves-themed has-tooltip action_hover_button share_whatsapp" style="margin-right: 10px; font-size: 18px;background-color: #d7d9dc !important;" target="_blank"><i class="fa-brands fa-whatsapp" style="font-size: 20px;" aria-hidden="true"></i></a>
                                                    <a href="javascript:void(0)" data-url="<?= urlencode(base_url('uploads/emp201/pdf/' . @$row->emp_201_file)) ?>" class="c_link btn btn-secondary-light btn-icon waves-effect waves-themed has-tooltip action_hover_button" style="font-size: 18px;background-color: #d7d9dc !important;"><i class="fa-solid fa-link" aria-hidden="true"></i></a>
                                                    <a data-url="<?= admin_url('employee_return/delete/' . $row->id) ?>" href="javascript:void(0)" class="btn btn-sm  delete_document" style="margin-right: 10px; font-size: 18px;background-color: #d7d9dc !important;"><i class="fa fa-trash"></i></a>
                                            
                                                
                                                </div>

                                            
                                            </td>
                                        </tr>
                                        
                                    <?php endforeach; ?>
                                </tbody>
                        </table>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>

<style>
    .filter_section { padding-left:25px; padding-right:25px; }
    table.table-bordered.dataTable tbody td, 
    table.table-bordered.dataTable tbody th {
        font-size: 20px !important;
        white-space: nowrap; /* Prevent text wrapping */
    }

    table.dataTable thead th {
        border-color: rgba(226, 232, 240, .7);
        border-top: 1px rgba(226, 232, 240, .7);
        border-style: solid;
        font-size: 20px !important;
    }

    .right_align {
        text-align: right;
    }

    .mnthly_return_heading {
        font-weight: 700;
        font-size: 33px;
    }

    .save_btn {
        font-size: 30px;
        padding-top: 17px;
        padding-bottom: 17px;
        padding-left: 17px;
        padding-right: 17px;
    }

    .panel-body:hover {
        box-shadow: none;
    }

    .dataTables_filter input {
        width: 160px !important;
        margin-right: 10px !important;
        margin-left: 10px !important;
    }

    .dtbl-footer {
        font-weight: bold !important;
    }

    html {
        background-color: #626f80 !important;
    }

    #tableinvoice tfoot th {
        font-weight: bold;
    }

    .dtsp-panesContainer {
        width: 88% !important;
    }

    .btn-default {
        font-size: 1.3em !important;
        margin-right: 10px !important;
        font-weight: bold;
    }

    table.dataTable {
        font-size: 1.1em !important;
    }

    table.dataTable th {
        font-size: 1.1em !important;
    }

    table.dataTable tr.dtrg-group.dtrg-level-1 td {
        font-size: 1.1em !important;
    }

    table.dataTable tr.odd {
        background-color: #F2F2F2 !important;
    }

    table.dataTable tr.even {
        background-color: #FFFFFF;
    }

    .dtrg-level-0 .hidden_row_group_column {
        line-height: 0 !important;
        font-size: 0 !important;
        color: transparent !important;
    }

    .hidden_table_header {
        visibility: hidden !important;
    }

    tr.group,
    tr.group:hover {
        background-color: #ddd !important;
    }

    .detail-content {
        padding: 10px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    /* Ensure columns adjust to content width */
    table.dataTable td,
    table.dataTable th {
        white-space: nowrap;
    }

    table.dataTable th,
    table.dataTable td {
        min-width: 100px; /* Adjust as necessary */
    }
    table.dataTable tr.odd {
      background-color: #fff !important;
    }
    table.dataTable tr.dtrg-group td {
        background-color: #E0FFFF  !important;
    }
</style>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowgroup/1.1.2/css/rowGroup.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css">

<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" charset="utf8" src="<?=base_url('assets/plugins/datatables/dataTables.buttons.js');?>"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/rowgroup/1.1.2/js/dataTables.rowGroup.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.colVis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap4-toggle/3.6.1/bootstrap4-toggle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.18/jspdf.plugin.autotable.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.0/dist/sweetalert2.min.css"> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.0/dist/sweetalert2.all.min.js"></script>


<script>
$(document).ready(function() {

    
    $(".c_link").click(function(e) {
        e.preventDefault();

        var downloadLink = $(this).attr("data-url");
        var decodedLink = decodeURIComponent(downloadLink);
        Swal.fire({
            icon: 'info',
            title: 'Secure download link',
            text: decodedLink,
        });
    });

    $('.email_document').on('click', function(e){
        e.preventDefault();
        var url = $(this).attr('data-link');
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to share this Emp201 via email?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, send it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(url, function(response) {
                    var result = JSON.parse(response);
                    if (result.success) {
                        Swal.fire('Success!', result.message, 'success');
                    } else {
                        Swal.fire('Failed!', result.message, 'error');
                    }
                    console.log(result.debug);  
                });
            }
        });
    });

    $(".delete_document").click(function() {
        
        var id = $(this).attr('data-id');
        let url = $(this).attr('data-url');

        Swal.fire({
            title: "Delete Emp201",
            text: "Are you sure you want to delete this Emp201?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                 
                $.get(url, function(response) {
                    console.log(response);

                    Swal.fire({
                        icon: "success",
                        text: "Emp201 Deleted",
                        title: "Emp201 Deleted"
                    }).then(function() {
                        location.reload();
                    });
                }).fail(function() {
                    Swal.fire({
                        icon: "error",
                        text: "Failed to delete Emp201. Please try again.",
                        title: "Error"
                    });
                });
            }
        });
    });


    $(document).on('click', '.view-pdf', function() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    var table = $('#emp_201').DataTable();
    var columns = [];
    var rows = [];

    // Get column headers
    table.columns().every(function() {
        var column = this.header();
        columns.push($(column).text());
    });

    // Get row data
    table.rows().every(function() {
        var row = this.data();
        rows.push(row);
    });

    // Adjust table content to fit one page
    const pageWidth = doc.internal.pageSize.getWidth();
    const pageHeight = doc.internal.pageSize.getHeight();

    doc.autoTable({
        head: [columns],
        body: rows,
        styles: {
            fontSize: 8, // Adjust font size to fit content
            cellPadding: 1,
            overflow: 'linebreak',
            halign: 'center',
            valign: 'middle'
        },
        headStyles: {
            fillColor: [22, 29, 38], // Dark blue header background
            textColor: [255, 255, 255], // White text
            fontSize: 10,
            halign: 'center'
        },
        footStyles: {
            fillColor: [22, 29, 38], // Dark blue footer background
            textColor: [255, 255, 255], // White text
            fontSize: 10,
            halign: 'center'
        },
        alternateRowStyles: {
            fillColor: [245, 245, 245] // Light grey alternate row background
        },
        columnStyles: {
            // Adjust widths to fit within the page
            0: {cellWidth: 20},
            1: {cellWidth: 20},
            2: {cellWidth: 20},
            3: {cellWidth: 20},
            4: {cellWidth: 20},
            5: {cellWidth: 20},
            6: {cellWidth: 20},
            7: {cellWidth: 20},
            8: {cellWidth: 20},
            9: {cellWidth: 20},
            // Add more if there are more columns
        },
        margin: {top: 20, bottom: 20, left: 10, right: 10},
        theme: 'striped', // Optional: theme for better visuals
        pageBreak: 'avoid', // Avoid page breaks to fit everything in one page
        tableWidth: 'auto', // Auto-adjust table width
    });

    doc.save('employee_return_details.pdf');
});



    var table = $('#emp_201').DataTable({
        scrollY: 450,
        scrollX: true,
        scrollCollapse: true,
        paging: false,
        order: [[17, 'desc']], 
        fixedHeader: {
            header: true,
            footer: true
        },
        responsive: true,
        processing: true,
        language: {
            loadingRecords: '&nbsp;',
            processing: 'Loading...'
        },
        rowReorder: true,
        dom: 'Bfrtip',
       buttons: [
            {
                extend: 'copyHtml5',
                text: '<i class="fa fa-files-o"></i>',
                titleAttr: 'Copy'
            },
            {
                extend: 'excelHtml5',
                footer: true,
                text: '<i class="fa fa-file-excel-o"></i>',
                titleAttr: 'Excel'
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fa fa-file-text-o"></i>',
                titleAttr: 'CSV'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa fa-file-pdf-o"></i>',
                titleAttr: 'PDF',
                download: 'open',
                footer: true,
                pageSize: 'A4',
                orientation: 'landscape',
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i>',
                titleAttr: 'Print',
                footer: true,
                pageSize: 'A4',
                orientation: 'landscape',
            },
            {
                extend: 'colvis',
                text: '<i class="fa fa-columns"></i>',
                titleAttr: 'Column Visibility'
            }
        ],
        columnDefs: [
            {
                targets: '_all',
                whiteSpace: 'nowrap' // Ensures all columns have nowrap
            }
        ],
        rowGroup: {
            dataSrc: [2], // Group by Client Code (index 2)
            startRender: function (rows, group) {
                // Create the custom header row
                var header = $('<tr class="group-header"><td colspan="15"><strong>' + group + '</strong></td></tr>');
                return header;
            },
            endRender: function (rows, group) {
                // Aggregate data for the footer
                var totalPay = 0;
                var totalSDL = 0;
                var totalUIF = 0;
                var totalLiability = 0;
                var totalPenalties = 0;
                var totalDue = 0;

                rows.nodes().to$().each(function() {
                    var row = $(this);
                    totalPay += parseFloat(row.find('td').eq(5).text().replace(/,/g, '') || 0);
                    totalSDL += parseFloat(row.find('td').eq(6).text().replace(/,/g, '') || 0);
                    totalUIF += parseFloat(row.find('td').eq(7).text().replace(/,/g, '') || 0);
                    totalLiability += parseFloat(row.find('td').eq(8).text().replace(/,/g, '') || 0);
                    totalPenalties += parseFloat(row.find('td').eq(9).text().replace(/,/g, '') || 0);
                    totalDue += parseFloat(row.find('td').eq(10).text().replace(/,/g, '') || 0);
                });

                var footer = $('<tr class="group-footer">' +
                               '<td colspan="5"></td>' +
                               '<td class="right_align">' + formatNumber(totalPay) + '</td>' +
                               '<td class="right_align">' + formatNumber(totalSDL) + '</td>' +
                               '<td class="right_align">' + formatNumber(totalUIF) + '</td>' +
                               '<td class="right_align">' + formatNumber(totalLiability) + '</td>' +
                               '<td class="right_align">' + formatNumber(totalPenalties) + '</td>' +
                               '<td class="right_align">' + formatNumber(totalDue) + '</td>' +
                               '<td colspan="5"></td>' +
                               '</tr>');

                return footer;
            }
        },
        initComplete: function() {
            $("body").removeClass("loading");
        }
    });

    // Function to format numbers with commas
    function formatNumber(num) {
        return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Calculate totals for all numeric columns
    function calculateTotals() {
        var totalPay = 0,
            totalSDL = 0,
            totalUIF = 0,
            totalLiability = 0,
            totalPenalties = 0,
            totalDue = 0;

        table.rows().every(function(rowIdx, tableLoop, rowLoop) {
            var row = this.node();
            totalPay += parseFloat($(row).find('td').eq(5).text().replace(/,/g, '') || 0);
            totalSDL += parseFloat($(row).find('td').eq(6).text().replace(/,/g, '') || 0);
            totalUIF += parseFloat($(row).find('td').eq(7).text().replace(/,/g, '') || 0);
            totalLiability += parseFloat($(row).find('td').eq(8).text().replace(/,/g, '') || 0);
            totalPenalties += parseFloat($(row).find('td').eq(9).text().replace(/,/g, '') || 0);
            totalDue += parseFloat($(row).find('td').eq(10).text().replace(/,/g, '') || 0);
        });

        return {
            totalPay: totalPay,
            totalSDL: totalSDL,
            totalUIF: totalUIF,
            totalLiability: totalLiability,
            totalPenalties: totalPenalties,
            totalDue: totalDue
        };
    }

    // Add totals row to the table footer
    function addTotalsRow() {
        var totals = calculateTotals();

        var footerHtml = '<tr class="totals-footer">' +
                         '<td colspan="5"><strong>Totals:</strong></td>' +
                         '<td class="right_align">' + formatNumber(totals.totalPay) + '</td>' +
                         '<td class="right_align">' + formatNumber(totals.totalSDL) + '</td>' +
                         '<td class="right_align">' + formatNumber(totals.totalUIF) + '</td>' +
                         '<td class="right_align">' + formatNumber(totals.totalLiability) + '</td>' +
                         '<td class="right_align">' + formatNumber(totals.totalPenalties) + '</td>' +
                         '<td class="right_align">' + formatNumber(totals.totalDue) + '</td>' +
                         '<td colspan="5"></td></tr>';

        $('#emp_201 tfoot').append(footerHtml);
    }

    // Call addTotalsRow once the table is initialized
    table.on('init', function() {
        addTotalsRow();
    });

    // Toggle row detail on plus/minus icon click
    $('#emp_201 tbody').on('click', 'a.toggle-row', function(e) {
        e.preventDefault();
        var $this = $(this);
        var id = $this.data('id');
        var $row = $this.closest('tr');

        // Check if the detail row already exists
        var $detailRow = $row.next('tr.detail-row');
        if ($detailRow.length) {
            // Toggle the detail row
            $detailRow.toggle();

            // Toggle icon
            if ($detailRow.is(':visible')) {
                $this.html('<i class="fa fa-minus-circle text-danger fa-2x"></i>');
            } else {
                $this.html('<i class="fa fa-plus-circle text-success fa-2x"></i>');
            }
        } else {
            // Create and insert the detail row
            var detailRowHtml = `
                <tr class="detail-row">
                    <td colspan="15">
                        <div class="detail-content">
                            <p><strong>YEAR END:</strong> ${$row.find('td').eq(1).text()}</p>
                            <p><strong>CLIENT CODE:</strong> ${$row.find('td').eq(2).text()}</p>
                            <p><strong>REFERENCES:</strong> ${$row.find('td').eq(3).text()}</p>
                            <p><strong>TAX PERIOD:</strong> ${$row.find('td').eq(4).text()}</p>
                            <p><strong>PAY:</strong> ${$row.find('td').eq(5).text()}</p>
                            <p><strong>SDL:</strong> ${$row.find('td').eq(6).text()}</p>
                            <p><strong>UIF:</strong> ${$row.find('td').eq(7).text()}</p>
                            <p><strong>LIABILITY:</strong> ${$row.find('td').eq(8).text()}</p>
                            <p><strong>PENALTIES:</strong> ${$row.find('td').eq(9).text()}</p>
                            <p><strong>DUE:</strong> ${$row.find('td').eq(10).text()}</p>
                        </div>
                    </td>
                </tr>`;

            $row.after(detailRowHtml);

            // Change icon
            $this.html('<i class="fa fa-minus-circle text-danger fa-2x"></i>');
        }
    });

    // Add event listener for search input to filter the table
    $('#searchInput').on('keyup', function() {
        table.search(this.value).draw();
    });


    $('#client_name, #financial_year, #filter_status').on('change', function() {
            table.draw();
        });

    // Custom filter function for DataTables
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            var clientName = $('#client_name').val();
            var financialYear = $('#financial_year').val();
            var filterStatus = $('#filter_status').val();

            var clientMatch = clientName === "" || data[2] === clientName;
            var yearMatch = financialYear === "" || data[1] === financialYear;
            var statusMatch = filterStatus === "" || data[15] === filterStatus;

            return clientMatch && yearMatch && statusMatch;
        }
    );


});







</script>

</html>
