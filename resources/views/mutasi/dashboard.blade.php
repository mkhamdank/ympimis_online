@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
thead input {
  width: 100%;
  padding: 3px;
  box-sizing: border-box;
}
thead>tr>th{
  text-align:center;
}
tbody>tr>td{
  text-align:center;
}
tfoot>tr>th{
  text-align:center;
}
td:hover {
  overflow: visible;
}
table.table-bordered{
  border:1px solid black;
}
table.table-bordered > thead > tr > th{
  border:1px solid black;
}
table.table-bordered > tbody > tr > td{
  border:1px solid rgb(211,211,211);
  padding-top: 0;
  padding-bottom: 0;
}
table.table-bordered > tfoot > tr > th{
  border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }
</style>
@stop
@section('content')
<section class="content">
  <div class="col-md-12" style="margin-top: 5px; padding:0 !important">
        <div id="chartundone" style="width: 100%"></div>
  </div>
  <div class="row">
    <div class="col-xs-12" style="text-align: center; color: red;">
      <span style="font-size: 3vw; color: red;"><i class="fa fa-angle-double-down"></i> Satu Departemen <i class="fa fa-angle-double-down"></i></span>
      <a href="{{ url("create/mutasi") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Input Mutasi</a>
      <a href="{{ url("dashboard_ant/mutasi") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">Antar Departemen</a>      
    </div>
  </div>
  <br>
  <div class="row">
        <div class="col-xs-12">
        <table id="tableResume" class="table table-bordered" style="width: 100%;margin-top: 5px !important">
                <thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 12px;font-weight: bold">
                    <tr>
                        <th style="padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Status</th>
                        <th style="padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">NIK</th>
                        <th style="width: 25%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Nama</th>
                        <th style="width: 15%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Departemen</th>
                        <th style="width: 15%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Ke Bagian</th>
                        <th style="width: 15%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Chief / Foreman</th>
                        <th style="width: 15%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Manager</th>
                        <th style="width: 15%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">General Manager</th>
                        <th style="padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Action</th>      
                    </tr>
                </thead>
                <tbody id="tableResumeBody">
                </tbody>               
            </table>
            </div>
    </div>
</section>
@endsection

@section('scripts')
<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>

<script>

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    jQuery(document).ready(function() {
        $('.select2').select2();
        $('body').toggleClass("sidebar-collapse");
        $('#modalLocation').modal({
                backdrop: 'static',
                keyboard: false
        });
        drawChart();
        fillTable();
    });
    function drawChart() {

    var datefrom = $('#datefrom').val();
    var dateto = $('#dateto').val();
    var department = $('#department').val();


    var data = {
      datefrom: datefrom,
      dateto: dateto,
      department: department, //.split(';')
    };

    $.get('{{ url("fetch/purchase_requisition/monitoring") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){

          var bulan = [], jml = [], dept = [], jml_dept = [], not_sign = [], sign = [], no_pr = [], belum_po = [], sudah_po = [], pr_close = [], belum_close = [], sudah_close = [];

          $.each(result.datas, function(key, value) {
            bulan.push(value.bulan);
            not_sign.push(parseInt(value.NotSigned));
            sign.push(parseInt(value.Signed));
          })

          $.each(result.data_pr_belum_po, function(key, value) {
            if (value.belum_po != 0) {
              no_pr.push(value.no_pr);
              belum_po.push(parseInt(value.belum_po));
              sudah_po.push(parseInt(value.sudah_po));              
            }
          })

          $.each(result.data_po_belum_receive, function(key, value) {
            if (value.belum_close != 0) {
              pr_close.push(value.no_pr);
              belum_close.push(parseInt(value.belum_close));
              sudah_close.push(parseInt(value.sudah_close));              
            }
          })

          $('#chart').highcharts({
            chart: {
              type: 'column'
            },
            title: {
              text: 'PR List By Month',
              style: {
                fontSize: '24px',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              categories: bulan,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1
            },
            yAxis: {
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
                title: {
                  enabled:false
                },
              tickInterval: 5,  
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
              reversed: true,
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
                      ShowModalPR(this.category,this.series.name,result.datefrom,result.dateto,result.department);
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
                name: 'Sign Not Completed',
                color: '#ff7043', //ff6666
                data: not_sign
              },
              {
                name: 'Sign Completed',
                color: '#00a65a',
                data: sign
              }
              // {
              //   name: 'Sign Not Completed',
              //   color: 'rgba(255, 0, 0, 0.25)',
              //   data: not_sign,
              //   type: 'spline'
              // },
              // {
              //   name: 'Sign Completed',
              //   color: '#5cb85c',
              //   data: sign,
              //   type: 'spline'
              // }
            ]
          })


          $('#chartundone').highcharts({
            chart: {
              type: 'column'
            },
            title: {
              text: 'Chart Aproval Mutasi Satu Departemen',
              style: {
                fontSize: '24px',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              categories: no_pr,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1
            },
            yAxis: {
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
              title: {
                enabled:false
              },
              tickInterval: 3,  
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
              reversed: true,
              itemStyle:{
                color: "black",
                fontSize: "12px",
                fontWeight: "normal",

              },
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModalPO(this.category,this.series.name,result.datefrom,result.dateto,result.department);
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
                name: 'Waiting',
                color: '#FFFF00',
                data: sudah_po
              },
              {
                name: 'Not Approved',
                color: '#dd4b39', //ff6666
                data: belum_po
              },
              {
                name: 'Approved',
                color: '#00a65a',
                data: sudah_po
              }
              
            ]
          })



          // $('#chartactual').highcharts({
          //   chart: {
          //     type: 'column'
          //   },
          //   title: {
          //     text: 'Outstanding PR Sudah PO (Belum Receive)',
          //     style: {
          //       fontSize: '24px',
          //       fontWeight: 'bold'
          //     }
          //   },
          //   xAxis: {
          //     type: 'category',
          //     categories: pr_close,
          //     lineWidth:2,
          //     lineColor:'#9e9e9e',
          //     gridLineWidth: 1
          //   },
          //   yAxis: {
          //     lineWidth:2,
          //     lineColor:'#fff',
          //     type: 'linear',
          //     title: {
          //       enabled:false
          //     },
          //     tickInterval: 3,  
          //     stackLabels: {
          //         enabled: true,
          //         style: {
          //             fontWeight: 'bold',
          //             color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
          //         }
          //     }
          //   },
          //   legend: {
          //     enabled:true,
          //     reversed: true,
          //     itemStyle:{
          //       color: "white",
          //       fontSize: "12px",
          //       fontWeight: "bold",

          //     },
          //   },
          //   plotOptions: {
          //     series: {
          //       cursor: 'pointer',
          //       point: {
          //         events: {
          //           click: function () {
          //             ShowModalActual(this.category,this.series.name,result.datefrom,result.dateto,result.department);
          //           }
          //         }
          //       },
          //       borderWidth: 0,
          //       dataLabels: {
          //         enabled: false,
          //         format: '{point.y}'
          //       }
          //     },
          //     column: {
          //         color:  Highcharts.ColorString,
          //         stacking: 'normal',
          //         borderRadius: 1,
          //         dataLabels: {
          //             enabled: true
          //         }
          //     }
          //   },
          //   credits: {
          //     enabled: false
          //   },

          //   tooltip: {
          //     formatter:function(){
          //       return this.series.name+' : ' + this.y;
          //     }
          //   },
          //   series: [
          //     {
          //       name: 'Belum Datang',
          //       color: '#ff7043', //ff6666
          //       data: belum_close
          //     },
          //     {
          //       name: 'Sudah Datang',
          //       color: '#00a65a',
          //       data: sudah_close
          //     }
          //   ]
          // })


        } else{
          alert('Attempt to retrieve data failed');
        }



      }
    })
  }

    function fillTable(){
        $.get('{{ url("fetch/mutasi/resume") }}', function(result, status, xhr){
            if(result.status){
                $('#tableResume').DataTable().clear();
                $('#tableResume').DataTable().destroy();
                var bodyResume = "";
                $('#tableResumeBody').html("");

                for (var i = 0; i < result.resumes.length; i++) {
                    bodyResume += '<tr>';

                    if (result.resumes[i].status == 'Approved') {
                    bodyResume += '<td style="background-color:#00a65a">'+result.resumes[i].status+'</td>';
                    }
                    else if (result.resumes[i].status == 'Not Approved') {
                    bodyResume += '<td style="background-color:#dd4b39">'+result.resumes[i].status+'</td>';
                    }
                    else{
                    bodyResume += '<td style="background-color:#FF8C00">'+result.resumes[i].status+'</td>';
                    };
                    bodyResume += '<td>'+result.resumes[i].mutasi_nik+'</td>';
                    bodyResume += '<td>'+result.resumes[i].mutasi_nama+'</td>';
                    bodyResume += '<td>'+result.resumes[i].mutasi_bagian+'</td>';
                    bodyResume += '<td>'+result.resumes[i].ke_section+'</td>';
                    //Chief or Foreman
                    if (result.resumes[i].chief_or_foreman == 'Approved') {
                    bodyResume += '<td style="background-color:#00a65a">'+result.resumes[i].chief_or_foreman+'</td>';
                    }
                    else if (result.resumes[i].chief_or_foreman == 'Not Approved') {
                    bodyResume += '<td style="background-color:#dd4b39">'+result.resumes[i].chief_or_foreman+'</td>';
                    }
                    else if (result.resumes[i].chief_or_foreman == 'Waiting') {
                    bodyResume += '<td style="background-color:#FFFF00">'+result.resumes[i].chief_or_foreman+'</td>';
                    }
                    else{
                    bodyResume += '<td style="background-color:#FF8C00">'+result.resumes[i].nama_chief+'</td>';
                    };
                    //Manager
                    if (result.resumes[i].manager == 'Approved') {
                    bodyResume += '<td style="background-color:#00a65a">'+result.resumes[i].manager+'</td>';
                    }
                    else if (result.resumes[i].manager == 'Not Approved') {
                    bodyResume += '<td style="background-color:#dd4b39">'+result.resumes[i].manager+'</td>';
                    }
                    else if (result.resumes[i].manager == 'Waiting') {
                    bodyResume += '<td style="background-color:#FFFF00">'+result.resumes[i].manager+'</td>';
                    }
                    else{
                    bodyResume += '<td style="background-color:#FF8C00">'+result.resumes[i].nama_manager+'</td>';
                    };
                    //GM
                    if (result.resumes[i].gm == 'Approved') {
                    bodyResume += '<td style="background-color:#00a65a">'+result.resumes[i].gm+'</td>';
                    }
                    else if (result.resumes[i].gm == 'Not Approved') {
                    bodyResume += '<td style="background-color:#dd4b39">'+result.resumes[i].gm+'</td>';
                    }
                    else if (result.resumes[i].gm == 'Waiting') {
                    bodyResume += '<td style="background-color:#FFFF00">'+result.resumes[i].gm+'</td>';
                    }
                    else{
                    bodyResume += '<td style="background-color:#FF8C00">'+result.resumes[i].nama_gm+'</td>';
                    };
                    bodyResume += '<td><a class="btn btn-danger btn-xs" href="{{ url("mutasi/show/") }}/'+result.resumes[i].id+'">Show</a></td>';
                    
                }

                $('#tableResumeBody').append(bodyResume);

                var table = $('#tableResume').DataTable({
                    'dom': 'Bfrtip',
                    'lengthMenu': [
                    [ 10, 25, 50, -1 ],
                    [ '10 rows', '25 rows', '50 rows', 'Show all' ]
                    ],
                    'buttons': {
                        buttons:[
                        {
                            extend: 'pageLength',
                            className: 'btn btn-default',
                        }
                        ]
                    },
                    'paging': true,
                    'lengthChange': true,
                    'pageLength': 10,
                    'searching'     : true,
                    'ordering'      : true,
                    'order': [],
                    'info'          : true,
                    'autoWidth'     : false,
                    "sPaginationType": "full_numbers",
                    "bJQueryUI": true,
                    "bAutoWidth": false,
                });
            }
            else{
                openErrorGritter('Error!', 'Upload Failed.');
                audio_error.play();
            }
        });
    }
</script>
@endsection