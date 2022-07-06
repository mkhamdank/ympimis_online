@extends('layouts.master')
@section('stylesheets')
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/dataTables.bootstrap4.min.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("css/buttons.dataTables.min.css")}}">
<style type="text/css">
    thead>tr>th{
        text-align:center;
        overflow:hidden;
    }
    tbody>tr>td{
        text-align:center;
    }
    tfoot>tr>th{
        text-align:center;
    }
    th:hover {
        overflow: visible;
    }
    td:hover {
        overflow: visible;
    }
    table.table-bordered{
        border:1px solid black;
    }
    table.table-bordered > thead > tr > th{
        border:1px solid black;
        padding-top: 0;
        padding-bottom: 0;
        vertical-align: middle;
    }
    table.table-bordered > tbody > tr > td{
        border:1px solid black;
        padding: 0px;
        vertical-align: middle;
    }
    table.table-bordered > tfoot > tr > th{
        border:1px solid black;
        padding:0;
        vertical-align: middle;
        background-color: rgb(126,86,134);
        color: #FFD700;
    }
    thead {
        background-color: rgb(126,86,134);
    }
    td{
        overflow:hidden;
        text-overflow: ellipsis;
    }
    #ngTemp {
        height:200px;
        overflow-y: scroll;
    }

    #ngList2 {
        height:454px;
        overflow-y: scroll;
        /*padding-top: 5px;*/
    }
    #loading, #error { display: none; }
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        /* display: none; <- Crashes Chrome on hover */
        -webkit-appearance: none;
        margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
    }

    .bootstrap-datetimepicker-widget { background-color: #fff !important; }

    .datepicker-days > table > thead,
    .datepicker-months > table > thead,
    .datepicker-years > table > thead,
    .datepicker-decades > table > thead,
    .datepicker-centuries > table > thead{
        background-color: white
    }

    input[type=number] {
        -moz-appearance:textfield; /* Firefox */
    }
    .page-wrapper{
        padding-top: 0px;
    }
</style>
@stop
@section('header')
    <div class="page-breadcrumb">
        <div class="row align-items-center">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="page-title mb-0 p-0">Dashboard</h3>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid" style="padding-top:0px">
        <div class="row">
            <!-- Auth::user()->role_code == "MIS"  -->
            @if (Auth::user()->role_code == "TRUE" || Auth::user()->role_code == "KBI" || Auth::user()->role_code == "ARISA")

            @elseif (Auth::user()->role_code == "MIS" || Auth::user()->role_code == "E - Billing")

            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body" style="background-color:#dd4b39">
                        <h4 class="card-title" style="color:#fff">Revised</h4>
                        <div class="text-end" style="color:#fff">
                            <h2 class="font-light mb-0" id="jumlah_revised">
                                <i class="ti-arrow-up text-success" ></i> 
                                0 Tagihan
                            </h2>
                            <i class="fa fa-rotate-left" style="font-size:24px"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
           <div class="col-sm-4">
                <div class="card">
                    <div class="card-body" style="background-color:#f9a825">
                        <h4 class="card-title" style="color:#fff">Check By Purchasing</h4>
                        <div class="text-end" style="color:#fff">
                            <h2 class="font-light mb-0" id="jumlah_purchasing">
                                <i class="ti-arrow-up text-success"></i> 
                                0 Tagihan
                            </h2>
                            <i class="fa fa-clock-o" style="font-size:24px"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body" style="background-color:#3f51b5">
                        <h4 class="card-title" style="color:#fff">Process By Accounting</h4>
                        <div class="text-end" style="color:#fff">
                            <h2 class="font-light mb-0"id="jumlah_accounting">
                                <i class="ti-arrow-up text-primary" ></i> 
                                0 Tagihan
                            </h2>
                            <i class="fa fa-money" style="font-size:24px"></i>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-sm-12">
                <div id="container" style="width: 100%"></div>
            </div>

            @elseif (Auth::user()->role_code == "E - Purchasing")

            <div class="col-sm-6">
                <div class="card">
                    <div class="card-body" style="background-color:#dd4b39">
                        <h4 class="card-title" style="color:#fff">Invoice Belum Cek</h4>
                        <div class="text-end" style="color:#fff">
                            <h2 class="font-light mb-0" id="pch_not_check">
                                <i class="ti-arrow-up text-success" ></i> 
                                0 Tagihan
                            </h2>
                            <i class="fa fa-close" style="font-size:24px"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="card">
                    <div class="card-body" style="background-color:#f9a825">
                        <h4 class="card-title" style="color:#fff">Invoice Belum Payment Request</h4>
                        <div class="text-end" style="color:#fff">
                            <h2 class="font-light mb-0" id="pch_not_payment">
                                <i class="ti-arrow-up text-success"></i> 
                                0 Tagihan
                            </h2>
                            <i class="fa fa-clock-o" style="font-size:24px"></i>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-sm-12">
                <div id="container_pch" style="width: 100%"></div>
            </div>
            
            @elseif (Auth::user()->role_code == "E - Accounting")

            <div class="col-sm-6">
                <div class="card">
                    <div class="card-body" style="background-color:#dd4b39">
                        <h4 class="card-title" style="color:#fff">Invoice Need Check</h4>
                        <div class="text-end" style="color:#fff">
                            <h2 class="font-light mb-0" id="acc_not_check">
                                <i class="ti-arrow-up text-success" ></i> 
                                0 Tagihan
                            </h2>
                            <i class="fa fa-close" style="font-size:24px"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="card">
                    <div class="card-body" style="background-color:#f9a825">
                        <h4 class="card-title" style="color:#fff">Invoice Need Payment</h4>
                        <div class="text-end" style="color:#fff">
                            <h2 class="font-light mb-0" id="acc_not_payment">
                                <i class="ti-arrow-up text-success"></i> 
                                0 Tagihan
                            </h2>
                            <i class="fa fa-clock-o" style="font-size:24px"></i>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-sm-12">
                <div id="container_acc" style="width: 100%"></div>
            </div>
            
            @endif

        </div>
    </div>
@stop
@section('scripts')
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="{{ url("js/jquery.dataTables.min.js") }}"></script>
<script src="{{ url("js/dataTables.bootstrap4.min.js") }}"></script>
<script src="{{ url("js/jquery.flot.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var user = "{{Auth::user()->role_code}}";

    jQuery(document).ready(function() {

        if (user == 'MIS' || user == 'E - Billing'){
            // fetchTable();
            drawChart();
        }

        if (user == 'E - Purchasing'){
            drawChartPch();
        }

        if (user == 'E - Accounting'){
            drawChartAcc();
        }
        
    });

    function drawChart() {
        $.get('{{ url("fetch/monitoring/invoice") }}', function(result, status, xhr) {
            if(xhr.status == 200){
                if(result.status){
                    var bulan = []
                    , jumlah = []
                    , purchasing = []
                    , accounting = []
                    , revised = []
                    , closed = []
                    , purchasing_all = []
                    , accounting_all = []
                    , revised_all = []
                    , closed_all = [];

                    $.each(result.datas, function(key, value) {
                        bulan.push(value.bulan);
                        jumlah.push(parseInt(value.jumlah));
                        purchasing.push(parseInt(value.purchasing));
                        accounting.push(parseInt(value.accounting));
                        revised.push(parseInt(value.revised));
                        closed.push(parseInt(value.closed));
                    });

                    $.each(result.data_outstanding, function(key, value) {
                        purchasing_all.push(parseInt(value.purchasing));
                        accounting_all.push(parseInt(value.accounting));
                        revised_all.push(parseInt(value.revised));
                        closed_all.push(parseInt(value.closed));

                        $('#jumlah_revised').html(revised_all+" Tagihan");
                        $('#jumlah_purchasing').html(purchasing_all+" Tagihan");
                        $('#jumlah_accounting').html(accounting_all+" Tagihan");
                    });

                    var date = new Date();

                    $('#container').highcharts({
                        chart: {
                            type: 'column',
                            height : '250px'
                        },
                        title: {
                            text: 'Data Outstanding Invoice By Month'
                        },
                        credits : {
                            enabled:false
                        },
                        xAxis: {
                            type: 'category',
                            categories: bulan
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Total Data Tagihan'
                            },
                            stackLabels: {
                                enabled: true,
                                style: {
                                    fontWeight: 'bold',
                            color: ( 
                                Highcharts.defaultOptions.title.style &&
                                Highcharts.defaultOptions.title.style.color
                                ) || 'gray'
                          }
                        },
                        tickInterval: 1
                      },

                      legend: {
                        align: 'right',
                        x: -30,
                        verticalAlign: 'top',
                        y: 25,
                        floating: true,
                        backgroundColor:
                        Highcharts.defaultOptions.legend.backgroundColor || 'white',
                        borderColor: '#CCC',
                        borderWidth: 1,
                        shadow: false,
                        enabled:false
                      },
                      tooltip: {
                        headerFormat: '<b>{point.x}</b><br/>',
                        pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                      },
                      plotOptions: {
                        column: {
                            stacking: 'normal',
                            dataLabels: {
                                enabled: true
                            }
                        }
                      },
                        series: [{
                            name: 'Revised',
                            color: '#ff6666',
                            data: revised
                        }, {
                            name: 'Check By Purchasing',
                            data: purchasing,
                            color : '#f0ad4e' 
                        },
                        {
                            name: 'Process By Accounting',
                            data: accounting,
                            color : '#448aff'
                        },
                        {
                            name: 'Closed',
                            data: closed,
                            color : '#5cb85c'
                        }
                        ]
                    })
                } else{
                    alert('Attempt to retrieve data failed');
                }
            }
        })
    }

    function drawChartPch() {
        $.get('{{ url("fetch/monitoring_pch/invoice") }}', function(result, status, xhr) {
            if(xhr.status == 200){
                if(result.status){
                    var supplier = []
                    , jumlah = []
                    , invoice_open = []
                    , invoice_not_payment = []
                    , invoice_open_all = []
                    , invoice_not_payment_all = [];

                    $.each(result.datas, function(key, value) {
                        if (value.invoice_open != "0" || value.invoice_not_payment != "0") {                        
                            supplier.push(value.supplier_name);
                            jumlah.push(parseInt(value.jumlah));
                            invoice_open.push(parseInt(value.invoice_open));
                            invoice_not_payment.push(parseInt(value.invoice_not_payment));
                        }
                    });

                    $.each(result.data_outstanding, function(key, value) {
                        invoice_open_all.push(parseInt(value.invoice_open));
                        invoice_not_payment_all.push(parseInt(value.invoice_not_payment));

                        $('#pch_not_check').html(invoice_open_all+" Tagihan");
                        $('#pch_not_payment').html(invoice_not_payment_all+" Tagihan");
                    });

                    var date = new Date();

                    $('#container_pch').highcharts({
                        chart: {
                            type: 'column',
                            height : '250px'
                        },
                        title: {
                            text: 'Data Outstanding Invoice By Vendor'
                        },
                        credits : {
                            enabled:false
                        },
                        xAxis: {
                            type: 'category',
                            categories: supplier
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Total Data Tagihan'
                            },
                            stackLabels: {
                                enabled: true,
                                style: {
                                    fontWeight: 'bold',
                            color: ( 
                                Highcharts.defaultOptions.title.style &&
                                Highcharts.defaultOptions.title.style.color
                                ) || 'gray'
                          }
                        },
                        tickInterval: 1
                      },

                      legend: {
                        align: 'right',
                        x: -30,
                        verticalAlign: 'top',
                        y: 25,
                        floating: true,
                        backgroundColor:
                        Highcharts.defaultOptions.legend.backgroundColor || 'white',
                        borderColor: '#CCC',
                        borderWidth: 1,
                        shadow: false,
                        enabled:false
                      },
                      tooltip: {
                        headerFormat: '<b>{point.x}</b><br/>',
                        pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                      },
                      plotOptions: {
                        column: {
                            stacking: 'normal',
                            dataLabels: {
                                enabled: true
                            }
                        }
                      },
                        series: [{
                            name: 'Invoice Not Checked',
                            color: '#ff6666',
                            data: invoice_open
                        }, {
                            name: 'Invoice Not Payment',
                            data: invoice_not_payment,
                            color : '#f0ad4e' 
                        }
                        ]
                    })
                } else{
                    alert('Attempt to retrieve data failed');
                }
            }
        })
    }

    function drawChartAcc() {
        $.get('{{ url("fetch/monitoring_acc/invoice") }}', function(result, status, xhr) {
            if(xhr.status == 200){
                if(result.status){
                    var supplier = []
                    , jumlah = []
                    , payment_invoice = []
                    , payment_invoice_all = []
                    , payment_jurnal = []
                    , payment_jurnal_all = [];

                    $.each(result.datas, function(key, value) {
                        if (value.invoice_payment_acc != "0" || value.invoice_bank != "0") {                        
                            supplier.push(value.supplier_name);
                            jumlah.push(parseInt(value.jumlah));
                            payment_invoice.push(parseInt(value.invoice_payment_acc));
                            payment_jurnal.push(parseInt(value.invoice_bank));
                        }
                    });

                    $.each(result.data_outstanding, function(key, value) {
                        payment_invoice_all.push(parseInt(value.invoice_payment_acc));
                        payment_jurnal_all.push(parseInt(value.invoice_bank));

                        $('#acc_not_check').html(payment_invoice_all+" Tagihan");
                        $('#acc_not_payment').html(payment_jurnal_all+" Tagihan");
                    });

                    var date = new Date();

                    $('#container_acc').highcharts({
                        chart: {
                            type: 'column',
                            height : '250px'
                        },
                        title: {
                            text: 'Data Outstanding Invoice By Vendor'
                        },
                        credits : {
                            enabled:false
                        },
                        xAxis: {
                            type: 'category',
                            categories: supplier
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Total Data Tagihan'
                            },
                            stackLabels: {
                                enabled: true,
                                style: {
                                    fontWeight: 'bold',
                            color: ( 
                                Highcharts.defaultOptions.title.style &&
                                Highcharts.defaultOptions.title.style.color
                                ) || 'gray'
                          }
                        },
                        tickInterval: 1
                      },

                      legend: {
                        align: 'right',
                        x: -30,
                        verticalAlign: 'top',
                        y: 25,
                        floating: true,
                        backgroundColor:
                        Highcharts.defaultOptions.legend.backgroundColor || 'white',
                        borderColor: '#CCC',
                        borderWidth: 1,
                        shadow: false,
                        enabled:false
                      },
                      tooltip: {
                        headerFormat: '<b>{point.x}</b><br/>',
                        pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                      },
                      plotOptions: {
                        column: {
                            stacking: 'normal',
                            dataLabels: {
                                enabled: true
                            }
                        }
                      },
                        series: [{
                            name: 'Invoice Not Checked',
                            color: '#ff6666',
                            data: payment_invoice
                        }, {
                            name: 'Invoice Not Payment',
                            data: payment_jurnal,
                            color : '#f0ad4e' 
                        }
                        ]
                    })
                } else{
                    alert('Attempt to retrieve data failed');
                }
            }
        })
    }
</script>


@endsection