@extends('layouts.display')
@section('stylesheets')
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
      color: #fff !important;
    }
    table.table-bordered > tfoot > tr > th{
      border:1px solid black;
      padding:0;
      vertical-align: middle;
      background-color: rgb(126,86,134);
      color: #fff !important;
    }
    thead {
      background-color: #fff;
      color: #fff;
    }
    a{

      color: #fff;
    }
    td{
      overflow:hidden;
        text-overflow: ellipsis;
    }
    th{
        color: white;
    }
    table.table-condensed > thead > tr > th{
      color: #54667a;
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
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
  <div class="container-fluid" style="padding-top: 10px;padding-left: 10px;padding-right: 10px">
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>
  <div class="row" style="padding-left: 10px; padding-right: 10px;">
    <div class="col-md-2">
        <div class="input-group date">
          <div class="input-group-addon bg-green" style="border: none; background-color: #469937; color: white;">
            <i class="fa fa-calendar" style="padding: 10px"></i>
          </div>
          <input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From">
        </div>
      </div>
      <div class="col-md-2" style="padding-left: 0;">
        <div class="input-group date">
          <div class="input-group-addon bg-green" style="border: none; background-color: #469937; color: white;">
            <i class="fa fa-calendar" style="padding: 10px"></i>
          </div>
          <input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To">
        </div>
      </div>

      <div class="col-md-12" style="padding-left: 0px;padding-top: 5px">
        <div id="chart" style="width: 100%;height: 60vh;"></div>
      </div>

      <div class="col-md-12" style="padding:0;overflow-x: auto;">
        <table id="tabelmonitor" class="table table-bordered" style="margin-top: 5px; width: 99%">
          <thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 12px;font-weight: bold">
            <tr>
              <th style="width: 2%; padding: 0;vertical-align: middle;font-size: 16px;background-color: #3f51b5;padding: 3px;color:white">Due Date</th>
              <th style="width: 9%; padding: 0;vertical-align: middle;font-size: 16px;background-color: #3f51b5;padding: 3px;color:white">Vendor</th>
              <th style="width: 4%; padding: 0;vertical-align: middle;font-size: 16px;background-color: #3f51b5;padding: 3px;color:white">Amount</th>
              <th style="width: 6%; padding: 0;vertical-align: middle;font-size: 16px;background-color: #3f51b5;padding: 3px;color:white">User</th>
              <th style="width: 6%; padding: 0;vertical-align: middle;font-size: 16px;background-color: #3f51b5;padding: 3px;color:white">Manager</th>
              <th style="width: 6%; padding: 0;vertical-align: middle;font-size: 16px;background-color: #3f51b5;padding: 3px;color:white">GM</th>
            </tr>
            </thead>
            <tbody id="tabelisi">
            </tbody>
            <tfoot>
            </tfoot>
          </table>
      </div>
    </div>
  </div>
</div>

  <div class="modal fade" id="myModal">
    <div class="modal-dialog" style="width:1250px;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="example2" class="table table-striped table-bordered table-hover" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Reff Number</th>
                    <th>Submission Date</th>
                    <th>Department</th>
                    <th>Applicant</th>
                    <th>Category</th>
                    <th>Subject</th>
                    <th>Type</th>
                    <th>Vendor</th>
                    <th>File</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

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

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
    
    $('#myModal').on('hidden.bs.modal', function () {
      $('#example2').DataTable().clear();
    });

    $('.select2').select2();

    $('.hideselect').next(".select2-container").hide();

    drawChart();
    fetchTable();
    setInterval(fetchTable, 300000);
  });



  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

  $('.datepicker').datepicker({
    autoclose: true,
    format: "dd-mm-yyyy",
    todayHighlight: true,
  });

  function getFormattedDate(date) {
    var year = date.getFullYear();

    var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
      "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
    ];

    var month = date.getMonth();

    var day = date.getDate().toString();
    day = day.length > 1 ? day : '0' + day;
    
    return day + '-' + monthNames[month] + '-' + year;
  }

  function drawChart() {

    var tglfrom = $('#tglfrom').val();
    var tglto = $('#tglto').val();

    var data = {
      tglfrom: tglfrom,
      tglto: tglto
    };

    $.get('{{ url("fetch/payment_request/monitoring") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){

          var bulan = [], not_sign = [], sign = [];

          $.each(result.datas, function(key, value) {
            bulan.push(value.bulan);
            not_sign.push(parseInt(value.NotSigned));
            sign.push(parseInt(value.Signed));
          })
       
          $('#chart').highcharts({
            chart: {
              type: 'column',
              zoomType: 'xy'
            },
            title: {
              text: 'Payment Request Monitoring',
              style: {
                fontSize: '24px',
                fontWeight: 'bold'
              }
            },
            subtitle: {
              text: 'On '+result.year+' Last 30 Days',
              style: {
                fontSize: '0.8vw',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              categories: bulan,
              crosshair: true
            },
            yAxis: {
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
                title: {
                  text: 'Jumlah Total Payment Request'
                },
              tickInterval: 1,  
              stackLabels: {
                  enabled: true,
                  style: {
                      fontWeight: 'bold',
                      color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                  }
              }
            },
            legend: {
              enabled:true,
              itemStyle:{
                color: "white",
                fontSize: "12px",
                fontWeight: "bold",
              },
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModal(this.category,this.series.name,result.tglfrom,result.tglto);
                    }
                  }
                },
                borderWidth: 0,
                dataLabels: {
                  enabled: false,
                  format: '{point.y}'
                }
              },
              column: {
                  color:  Highcharts.ColorString,
                  stacking: 'normal',
                  borderRadius: 1,
                  dataLabels: {
                      enabled: true
                  }
              }
            },
            credits: {
              enabled: false
            },

            tooltip: {
              formatter:function(){
                return this.series.name+' : ' + this.y;
              }
            },
            series: [
              {
                name: 'Incompleted',
                color: '#ff6666',
                data: not_sign
              },
              {
                name: 'Completed',
                color: '#00a65a',
                data: sign
              }
            ]
          })
        } else{
          alert('Attempt to retrieve data failed');
        }

      }
    })
  }

  

  function fetchTable(){

    var datefrom = $('#datefrom').val();
    var dateto = $('#dateto').val();

    var data = {
      datefrom: datefrom,
      dateto: dateto
    };

    $.get('{{ url("fetch/payment_request/table") }}', data, function(result, status, xhr){
      if(xhr.status == 200){
        if(result.status){

          $("#tabelisi").find("td").remove();  
          $('#tabelisi').html("");
          
          var table = "";
          var user = "";
          var manager = "";
          var gm = "";

          $.each(result.datas, function(key, value) {

            var applicant = value.created_name;
            var applicantname = applicant.split(' ').slice(0,2).join(' ');
            var colorapplicant = "";

            var managername = value.manager_name;
            
            if(value.status_manager != null) {
              var manager_approval = value.status_manager.split('/');
              var managerdate = manager_approval[1];

            }
            var colormanager = "";

            var gmname = value.gm_name;
            
            if (value.status_gm != null) {
              var gm_approval = value.status_gm.split('/');;
              var gmdate = gm_approval[1];
            }
            var colorgm = "";

            var d = 0;

            // var urldetail = '{{ url("payment_request/detail/") }}';
            var urlreport = '{{ url("report/payment_request/") }}';
            var urlverifikasi = '{{ url("payment_request/verifikasi/") }}';
            // var urlcheck = '{{ url("payment_request/check/") }}';

            //User / Applicant
              if (value.posisi != "user") {
                user = '<a href="'+urlreport+'/'+value.id+'">'+applicantname+'<br>'+getFormattedDate(new Date(value.payment_date))+'</a>';
                colorapplicant = 'style="background-color:#00a65a"';
              }
              else {
                if (d == 0) {  
                    user = '<a href="'+urldetail+'/'+value.id+'">'+applicantname+'<br>'+getFormattedDate(new Date(value.payment_date))+'</a>';
                    colorapplicant = 'style="background-color:#dd4b39"';                    
                    d = 1;
                  } else {
                    user = '';
                  }
              } 
              //Manager
              if (value.manager != null) {
                if (value.status_manager != null) {
                    if (value.posisi == "manager") {
                        if (d == 0) {  
                            manager = '<a href="'+urlverifikasi+'/'+value.id+'">'+managername+'<br>Waiting</a>';   
                            colormanager = 'style="background-color:#dd4b39"';                  
                            d = 1;
                          } else {
                            manager = '';
                          }
                    }
                    else{
                        manager = '<a href="'+urlreport+'/'+value.id+'">'+managername+'<br>'+getFormattedDate(new Date(managerdate))+'</a>';
                        colormanager = 'style="background-color:#00a65a"'; 
                    }
                }
                else{
                  if (d == 0) {  
                    manager = '<a href="'+urlverifikasi+'/'+value.id+'">'+managername+'<br>Waiting</a>'; 
                    colormanager = 'style="background-color:#dd4b39"';                  
                    d = 1;
                  } else {
                    manager = '';
                  }
                }
              }
              else{
                manager = '<span style="color:white">None</span>'; 
                colormanager = 'style="background-color:#424242"';
              }

            
              //GM
              if (value.gm != null) {
                if (value.status_gm != null) {
                    if (value.posisi == "gm") {
                        if (d == 0) {  
                          gm = '<a href="'+urlverifikasi+'/'+value.id+'">'+gmname+'<br>Waiting</a>';
                          colorgm = 'style="background-color:#dd4b39"'; 
                          d = 1;
                        } else {
                          gm = '';
                        }
                    } else {
                      gm = '<a href="'+urlreport+'/'+value.id+'">'+gmname+'<br>'+getFormattedDate(new Date(gmdate))+'</a>'; 
                      colorgm = 'style="background-color:#00a65a"'; 
                    }
                } 

                else {
                  if (d == 0) {  
                    gm = '<a href="'+urlverifikasi+'/'+value.id+'">'+gmname+'<br>Waiting</a>';
                    colorgm = 'style="background-color:#dd4b39"'; 
                    d = 1;
                  } else {
                    gm = '';
                  }
                }
              }else{
                gm = '<span style="color:white">None</span>';
                colorgm = 'style="background-color:#424242"';
              }
              table += '<tr style="font-size:16px">';
              table += '<td>'+getFormattedDate(new Date(value.payment_due_date))+'</td>';
              table += '<td>'+value.supplier_code+' - '+value.supplier_name+'</td>';
              table += '<td>'+value.currency+ ' '+formatUang(value.amount,"")+'</td>';
              table += '<td '+colorapplicant+'>'+user+'</td>';
              table += '<td '+colormanager+'>'+manager+'</td>';
              table += '<td '+colorgm+'>'+gm+'</td>';
          })

          $('#tabelisi').append(table);
        }
      }
    })
  }

   function formatUang(angka, prefix) {
    var angka_int = parseInt(angka);
    var number_string = angka_int.toString().replace(/[^,\d]/g, ""),
    split = number_string.split(","),
    sisa = split[0].length % 3,
    rupiah = split[0].substr(0, sisa),
    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

      // tambahkan titik jika yang di input sudah menjadi angka ribuan
      if (ribuan) {
        separator = sisa ? "." : "";
        rupiah += separator + ribuan.join(".");
      }

      rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
      return prefix == undefined ? rupiah : rupiah ? "" + rupiah : "";
  }

  function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }

  function ShowModal(week, status, tglfrom, tglto) {
    tabel = $('#example2').DataTable();
    tabel.destroy();

    $("#myModal").modal("show");

    var table = $('#example2').DataTable({
      'dom': 'Bfrtip',
      'responsive': true,
      'lengthMenu': [
      [ 10, 25, 50, -1 ],
      [ '10 rows', '25 rows', '50 rows', 'Show all' ]
      ],
      'buttons': {
        buttons:[
        {
          extend: 'pageLength',
          className: 'btn btn-default',
          // text: '<i class="fa fa-print"></i> Show',
        },
        {
          extend: 'copy',
          className: 'btn btn-success',
          text: '<i class="fa fa-copy"></i> Copy',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        {
          extend: 'excel',
          className: 'btn btn-info',
          text: '<i class="fa fa-file-excel-o"></i> Excel',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        {
          extend: 'print',
          className: 'btn btn-warning',
          text: '<i class="fa fa-print"></i> Print',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        ]
      },
      'paging': true,
      'lengthChange': true,
      'searching': true,
      'ordering': true,
      'order': [],
      'info': true,
      'autoWidth': true,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
      "bAutoWidth": false,
      "processing": true,
      "serverSide": true,
      "ajax": {
           "type" : "get",
          "url" : "{{ url('investment/detail') }}",
          "data" : {
            week : week,
            status : status,
            tglfrom : tglfrom,
            tglto : tglto
          }
        },
      "columns": [
          { "data": "reff_number" },
          { "data": "submission_date" },
          { "data": "applicant_department" },
          { "data": "applicant_name" },
          { "data": "category" },
          { "data": "subject" },
          { "data": "type" },
          { "data": "supplier_code" },
          { "data": "file" },
          { "data": "status" },
          { "data": "action" },
        ]    });

    $('#judul_table').append().empty();
    $('#judul_table').append('<center><b>'+status+' week '+week+'</center></b>');
    
  }

  Highcharts.createElement('link', {
          href: '{{ url("fonts/UnicaOne.css")}}',
          rel: 'stylesheet',
          type: 'text/css'
        }, null, document.getElementsByTagName('head')[0]);

        Highcharts.theme = {
          colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
          '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
          chart: {
            backgroundColor: {
              linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
              stops: [
              [0, '#2a2a2b']
              ]
            },
            style: {
              fontFamily: 'sans-serif'
            },
            plotBorderColor: '#606063'
          },
          title: {
            style: {
              color: '#E0E0E3',
              textTransform: 'uppercase',
              fontSize: '20px'
            }
          },
          subtitle: {
            style: {
              color: '#E0E0E3',
              textTransform: 'uppercase'
            }
          },
          xAxis: {
            gridLineColor: '#707073',
            labels: {
              style: {
                color: '#E0E0E3'
              }
            },
            lineColor: '#707073',
            minorGridLineColor: '#505053',
            tickColor: '#707073',
            title: {
              style: {
                color: '#A0A0A3'

              }
            }
          },
          yAxis: {
            gridLineColor: '#707073',
            labels: {
              style: {
                color: '#E0E0E3'
              }
            },
            lineColor: '#707073',
            minorGridLineColor: '#505053',
            tickColor: '#707073',
            tickWidth: 1,
            title: {
              style: {
                color: '#A0A0A3'
              }
            }
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.85)',
            style: {
              color: '#F0F0F0'
            }
          },
          plotOptions: {
            series: {
              dataLabels: {
                color: 'white'
              },
              marker: {
                lineColor: '#333'
              }
            },
            boxplot: {
              fillColor: '#505053'
            },
            candlestick: {
              lineColor: 'white'
            },
            errorbar: {
              color: 'white'
            }
          },
          legend: {
            // itemStyle: {
            //   color: '#E0E0E3'
            // },
            // itemHoverStyle: {
            //   color: '#FFF'
            // },
            // itemHiddenStyle: {
            //   color: '#606063'
            // }
          },
          credits: {
            style: {
              color: '#666'
            }
          },
          labels: {
            style: {
              color: '#707073'
            }
          },

          drilldown: {
            activeAxisLabelStyle: {
              color: '#F0F0F3'
            },
            activeDataLabelStyle: {
              color: '#F0F0F3'
            }
          },

          navigation: {
            buttonOptions: {
              symbolStroke: '#DDDDDD',
              theme: {
                fill: '#505053'
              }
            }
          },

          rangeSelector: {
            buttonTheme: {
              fill: '#505053',
              stroke: '#000000',
              style: {
                color: '#CCC'
              },
              states: {
                hover: {
                  fill: '#707073',
                  stroke: '#000000',
                  style: {
                    color: 'white'
                  }
                },
                select: {
                  fill: '#000003',
                  stroke: '#000000',
                  style: {
                    color: 'white'
                  }
                }
              }
            },
            inputBoxBorderColor: '#505053',
            inputStyle: {
              backgroundColor: '#333',
              color: 'silver'
            },
            labelStyle: {
              color: 'silver'
            }
          },

          navigator: {
            handles: {
              backgroundColor: '#666',
              borderColor: '#AAA'
            },
            outlineColor: '#CCC',
            maskFill: 'rgba(255,255,255,0.1)',
            series: {
              color: '#7798BF',
              lineColor: '#A6C7ED'
            },
            xAxis: {
              gridLineColor: '#505053'
            }
          },

          scrollbar: {
            barBackgroundColor: '#808083',
            barBorderColor: '#808083',
            buttonArrowColor: '#CCC',
            buttonBackgroundColor: '#606063',
            buttonBorderColor: '#606063',
            rifleColor: '#FFF',
            trackBackgroundColor: '#404043',
            trackBorderColor: '#404043'
          },

          legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
          background2: '#505053',
          dataLabelsColor: '#B0B0B3',
          textColor: '#C0C0C0',
          contrastTextColor: '#F0F0F3',
          maskColor: 'rgba(255,255,255,0.3)'
        };
        Highcharts.setOptions(Highcharts.theme);
      

  function openSuccessGritter(title, message){
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-success',
      image: '{{ url("images/image-screen.png") }}',
      sticky: false,
      time: '3000'
    });
  }

  function openErrorGritter(title, message) {
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-danger',
      image: '{{ url("images/image-stop.png") }}',
      sticky: false,
      time: '3000'
    });
  }

</script>
@stop