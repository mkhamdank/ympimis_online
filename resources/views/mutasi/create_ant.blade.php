@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<style type="text/css">
    input {
        line-height: 22px;
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
    }
    table.table-bordered > tfoot > tr > th{
        border:1px solid rgb(211,211,211);
    }
    #loadingscreen { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
  <h1>
    Pengajuan Form Mutasi <span class="text-purple">突然変異フォームの提出</span>
  </h1>
  <ol class="breadcrumb">
  </ol>
</section>
@stop

@section('content')
<meta name="csrf-token">
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-header">
        </div>
        <div class="col-md-2">
        </div>
        <form class="form-horizontal" role="form" method="post" action="{{url('create_ant/mutasi')}}">
            {{ csrf_field() }}
          <div class="box-body">
            <div class="col-md-12 col-md-offset-3">
              <div class="col-md-6">
                <div class="form-group">
                  <div class="row mb-3">
                  <label for="colFormLabel" class="col-sm-2 col-form-label">NIK</label>
                      <div class="col-sm-10">
                        <select class="form-control select2" id="employee_id" name="employee_id" data-placeholder='Pilih NIK' style="width: 100%" required onchange="checkEmp(this.value)">
                          <option value="">&nbsp;</option>
                          @foreach($user as $row)
                          <option value="{{$row->employee_id}}">{{$row->employee_id}} - {{$row->name}}</option>
                          @endforeach
                        </select>
                      </div>
                  </div>
                </div>
                <div class="form-group" hidden="hidden">
                  <div class="row mb-3">
                  <label for="colFormLabel" class="col-sm-2 col-form-label" id="labelnama">Nama</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="name" name="name" readonly>
                      </div>
                  </div>
                </div> 
                <div class="form-group">
                  <div class="row mb-3">
                  <label for="colFormLabel" class="col-sm-2 col-form-label" id="labeldept">Sub Seksi</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="group" name="group" readonly>
                      </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row mb-3">
                  <label for="colFormLabel" class="col-sm-2 col-form-label" id="labeldept">Seksi</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="section" name="section" readonly>
                      </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row mb-3">
                  <label for="colFormLabel" class="col-sm-2 col-form-label" id="labeldept">Departemen</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="department" name="department" readonly>
                      </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row mb-3">
                  <label for="colFormLabel" class="col-sm-2 col-form-label" id="labelposition">Jabatan</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="position" name="position" readonly>
                      </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row mb-3">
                  <label for="colFormLabel" class="col-sm-2 col-form-label">Rekomendasi Atasan</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="rekom" name="rekom" value="MUTASI KE DEPARTEMEN LAIN" readonly>
                      </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row mb-3">
                  <label for="colFormLabel" class="col-sm-2 col-form-label">Ke Sub Seksi</label>
                      <div class="col-sm-10">
                        <select class="form-control select2" id="ke_sub_seksi" name="ke_sub_seksi" data-placeholder='Pilih Bagian' style="width: 100%" required="">
                          <option value="">&nbsp;</option>
                          @foreach($group as $row)
                          <option value="{{$row->group}}">{{$row->group}}</option>
                          @endforeach
                        </select>
                      </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row mb-3">
                  <label for="colFormLabel" class="col-sm-2 col-form-label">Seksi</label>
                      <div class="col-sm-10">
                        <select class="form-control select2" id="ke_seksi" name="ke_seksi" data-placeholder='Pilih Bagian' style="width: 100%" required="">
                          <option value="">&nbsp;</option>
                          @foreach($section as $row)
                          <option value="{{$row->section}}">{{$row->section}}</option>
                          @endforeach
                        </select>
                      </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row mb-3">
                  <label for="colFormLabel" class="col-sm-2 col-form-label">Ke Departemen</label>
                      <div class="col-sm-10">
                        <select class="form-control select2" id="ke_departemen" name="ke_departemen" data-placeholder='Pilih Bagian' style="width: 100%" required="">
                          <option value="">&nbsp;</option>
                          @foreach($dept as $row)
                          <option value="{{$row->department}}">{{$row->department}}</option>
                          @endforeach
                        </select>
                      </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row mb-3">
                  <label for="colFormLabel" class="col-sm-2 col-form-label">Jabatan</label>
                      <div class="col-sm-10">
                        <select class="form-control select2" id="ke_jabatan" name="ke_jabatan" data-placeholder='Pilih Bagian' style="width: 100%" required="">
                          <option value="">&nbsp;</option>
                          @foreach($post as $row)
                          <option value="{{$row->position}}">{{$row->position}}</option>
                          @endforeach
                        </select>
                      </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row mb-3">
                  <label for="colFormLabel" class="col-sm-2 col-form-label">Tanggal Mutasi</label>
                      <div class="col-sm-10">
                        <input type="date" class="form-control" id="tanggal" name="tanggal">
                      </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row mb-3">
                  <label for="colFormLabel" class="col-sm-2 col-form-label">Alasan Mutasi</label>
                      <div class="col-sm-10">
                        <textarea class="form-control" id="alasan" name="alasan" rows="3"></textarea>
                      </div>
                  </div>
                </div>
                <div class="form-group pull-right">
                  <br>
                  <a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</section>
@endsection

@section('scripts')
<script>
    jQuery(document).ready(function() {
        $('.select2').select2();
        $('body').toggleClass("sidebar-collapse");
        $('#labelnama').show();
        $('#name').show();
        $('#labeldept').show();
        $('#department').show();
        $('#labelposition').show();
        $('#position').show();
        $('#group').show();
        $('#section').show();
    });
    function checkEmp(value) {
        if (value.length == 9) {
            var data = {
                employee_id:$('#employee_id').val(),
                department:$('#department').val(),
                section:$('#section').val()
              }

              $.get('{{ url("dashboard/mutasi/get_employee") }}',data, function(result, status, xhr){
                  if(result.status){
                    $('#labelnama').show();
                    $('#name').show();
                    $('#labeldept').show();
                    $('#department').show();
                    $('#labelposition').show();
                    $('#position').show();
                    $('#group').show();
                    $('#section').show();
                    $.each(result.employee, function(key, value) {
                        $('#name').val(value.name);
                        $('#department').val(value.department);
                        $('#position').val(value.position);
                        $('#group').val(value.group);
                        $('#section').val(value.section);
                    });
                  }
                  else{
                    alert('NIK Tidak ditemukan');
                  }

              });
        }else{
            $('#labelnama').show();
            $('#name').show();
            $('#labeldept').show();
            $('#department').show();
            $('#labelposition').show();
            $('#position').show();
            $('#group').show();
            $('#section').show();
        }
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(function () {
        $('.select3').select2({
            dropdownParent: $('#modalDetail')
        });
    })

    $(function () {
        $('.select4').select2({
            dropdownParent: $('#modalCreate')
        });
    })

    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

    function openErrorGritter(title, message) {
        jQuery.gritter.add({
            title: title,
            text: message,
            class_name: 'growl-danger',
            image: '{{ url("images/image-stop.png") }}',
            sticky: false,
            time: '2000'
        });
    }

    function openSuccessGritter(title, message){
        jQuery.gritter.add({
            title: title,
            text: message,
            class_name: 'growl-success',
            image: '{{ url("images/image-screen.png") }}',
            sticky: false,
            time: '2000'
        });
    }
  
    function clearConfirmation(){
        location.reload(true);
    }
</script>
@endsection