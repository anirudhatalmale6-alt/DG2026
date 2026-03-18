<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); 
$CI = & get_instance();
?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s mbot10" style="margin-bottom: -6px;">
                    <div class="panel-body _buttons">
                        <div class="col-md-12">
                           <h4 class="bold"><?= $document->client_code ?></h4> 
                           <hr> 
                           <a href="<?= base_url('uploads/emp201/pdf/' . $document->emp_201_file); ?>" class="btn btn-primary waves-effect waves-themed">Download</a>
                           <a href="<?= base_url('admin/employee_return'); ?>" class="btn btn-danger waves-effect waves-themed">Close</a>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="panel_s mbot10" style="margin-bottom: -6px;">
                    <div class="panel-body _buttons">
                        <?php
                            $file_url = base_url('uploads/emp201/pdf/' . $document->emp_201_file);
                          
                            echo '<iframe src="'.$file_url.'" style="width:100%; height:500px;" frameborder="0"></iframe>';
                        ?>
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
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowgroup/1.1.2/css/rowGroup.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css">

<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" charset="utf8" src="<?=base_url('assets/plugins/datatables/dataTables.buttons.js');?>"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/rowgroup/1.1.2/js/dataTables.rowGroup.min.js"></script>
</html>
