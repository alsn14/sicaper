@extends('layouts.app')
@section('title', __(""))
@section('content')
<x-head-datatable/>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card w-100">
                <div class="card-header row">
                <div class="d-flex justify-content-between align-items-center mb-3 w-100">
                    <div class="d-flex justify-content-start align-items-center">
                        <h5 class="mb-0">{{ __("Daftar Ruang") }}</h5>
                    </div>
                    <button class="btn btn-success" type="button"  data-toggle="modal" data-target="#TambahData" id="modal-button"><i class="fas fa-plus"></i> {{ __("Tambah Data") }}</button>
                    <!-- @if(Auth::user()->role->name != 'staff')
                    
                    @endif -->
                </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3" id="item-count" style="display:none;">
                        <div class="col-md-12">
                            <h5 class="text-center"> {{ __("Ubah Data") }}</h5>
                        </div>
                    </div>


                <!-- Modal -->
                <div class="modal fade" id="TambahData" tabindex="-1" aria-labelledby="TambahDataModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="TambahDataModalLabel">{{ __("add goods") }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"  >&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label for="nama" class="form-label">{{ __("Nama Ruang") }} <span class="text-danger">*</span></label>
                                        <input type="text" name="nama" class="form-control" autocomplete="off">
                                        <input type="hidden" name="id"/>
                                    </div>
                                </div>  
                                <div class="col-md-7">
                                    <div class="form-group">   
                                        <label for="pic_id" class="form-label">{{ __("Penanggung Jawab") }} <span class="text-danger">*</span></label>
                                        <select name="pic_id" class="form-control">
                                            <option value="">-- {{ __("select penanggung jawab") }} --</option>
                                            @foreach ($penanggungjawab as $jb)
                                                <option value="{{$jb->id}}">{{$jb->name}}</option>
                                            @endforeach
                                        </select> 
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" id="kembali">{{ __("back") }}</button>
                            <button type="button" class="btn btn-success" id="simpan">{{ __("save") }}</button>
                        </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="data-tabel" width="100%"  class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" width="8%">{{ __("no") }}</th>
                                    <th class="border-bottom-0">{{ __("name") }}</th>
                                    <th class="border-bottom-0">{{ __("penanggung jawab") }}</th>
                                    <th class="border-bottom-0" width="1%">{{ __("action") }}</th>
                                    <!-- @if(Auth::user()->role->name != 'staff')
                    
                    @endif -->
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<x-data-table/>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function isi(){
        $('#data-tabel').DataTable({
            lengthChange: true,
            processing:true,
            serverSide:true,
            ajax:`{{route('room.list')}}`,
            columns:[
                {
                    "data":null,"sortable":false,
                    render:function(data,type,row,meta){
                        return meta.row + meta.settings._iDisplayStart+1;
                    }
                },
               {
                    data:'name',
                    name:'name'
                },
                {
                    data:'pic_id',
                    name:'pic_id'
                },
                
                {
                    data:'tindakan',
                    name:'tindakan'
                }
                
            ]
        }).buttons().container();
    }

    function simpan(){
        const name = $("input[name='nama']").val();
        const pic_id = $("select[name='pic_id']").val();
        const Form = new FormData();
        Form.append('name', name);
        Form.append('pic_id', pic_id);
        if(name.length == 0){
            return Swal.fire({
                position: "center",
                icon: "warning",
                title: "nama tidak boleh kosong !",
                showConfirmButton: false,
                imer: 1500
            });
        }
        $.ajax({
                url:`{{route('room.save')}}`,
                type:"post",
                processData: false,
                contentType: false,
                dataType: 'json',
                data:Form,
                success:function(res){
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: res.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#kembali').click();
                    $("input[name='nama']").val(null);
                    $("select[name='pic_id']").val(null);
                    $('#data-tabel').DataTable().ajax.reload();
                },
                error:function(err){
                    console.log(err);
            },

        });
    }


    function ubah(){
        const name = $("input[name='nama']").val();
        const pic_id = $("select[name='pic_id']").val();
        const Form = new FormData();
        Form.append('id', $("input[name='id']").val());
        Form.append('name', name);
        Form.append('pic_id', pic_id);
        $.ajax({
                url:`{{route('room.update')}}`,
                type:"post",
                contentType: false,
                processData: false,
                dataType: 'json',
                data:Form,
                success:function(res){
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: res.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#kembali').click();
                    $("input[name='id']").val(null);
                    $("input[name='nama']").val(null);
                    $("select[name='pic_id']").val(null);
                    $('#data-tabel').DataTable().ajax.reload();
                },
                error:function(err){
                    console.log(err);
            },


        });
    }

    $(document).ready(function(){
        isi();

        $('#simpan').on('click',function(){
            if($(this).text() === 'Simpan Perubahan'){
                ubah();
            }else{
                simpan();
            }
        });

        $("#modal-button").on("click",function(){
            $("#item-count").hide();
            $("input[name='nama']").val(null);
            $("input[name='id']").val(null);
            $("#simpan").text("Simpan");
        });


    });



    $(document).on("click",".ubah",function(){
        let id = $(this).attr('id');
        $("#modal-button").click();
        $("#item-count").show();
        $("#simpan").text("Simpan Perubahan");
        $.ajax({
            url:"{{route('room.detail')}}",
            type:"post",
            data:{
                id:id,
                "_token":"{{csrf_token()}}"
            },
            success:function({data}){
                $("input[name='id']").val(data.id);
                $("input[name='nama']").val(data.name);
                $("select[name='pic_id']").val(data.pic_id);
            }
        });


    });

    $(document).on("click",".hapus",function(){
        let id = $(this).attr('id');
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success m-1",
                cancelButton: "btn btn-danger m-1"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: "Anda Yakin ?",
            text: "Data Ini Akan Di Hapus",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya,Hapus",
            cancelButtonText: "Tidak, Kembali!",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url:"{{route('room.delete')}}",
                    type:"delete",
                    data:{
                        id:id,
                        "_token":"{{csrf_token()}}"
                    },
                    success:function(res){
                        Swal.fire({
                                position: "center",
                                icon: "success",
                                title: res.message,
                                showConfirmButton: false,
                                timer: 1500
                        });
                        $('#data-tabel').DataTable().ajax.reload();
                    }
                });
            }
        });


    });


</script>

@endsection
