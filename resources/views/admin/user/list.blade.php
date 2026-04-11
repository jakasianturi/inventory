@extends('admin.layouts.app')
@section('title', 'Daftar siswa')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Daftar Akun</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Daftar Akun</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <!-- Page Heading -->
                {{-- <div class="d-sm-flex align-items-center justify-content-start mb-4">
                    <a href="{{ route('admin.users.create') }}"
                        class="d-sm-inline-block mt-mw-sm-2 btn btn-sm btn-primary shadow-sm"><i
                            class="fas fa-plus fa-sm mr-1"></i>Tambah Siswa</a> --}}
                </div>
                @if (session('message'))
                    <div class="alert alert-success alert-dismissible fade show mb-4 border-left-success mb-4"
                        role="alert">
                        {{ session('message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <!-- User List -->
                <div class="card card-primary card-outline  mb-4">
                    <div class="card-body">
                        <table class="table table-bordered table-striped" id="dataakun" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 30%">Nama</th>
                                    <th scope="col" style="width: 10%">Jenis Kelamin</th>
                                    <th scope="col" style="width: 20%">Email</th>
                                    <th scope="col" class="text-center" style="width: 20%">&nbsp;</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <!-- Delete Modal -->
                <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel">Hapus</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Apakah Anda yakin ingin menghapus Akun?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button id="deleteData" type="button" class="btn btn-danger">Hapus Akun</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('customStyle')
    <script>
        // CSRF
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
        // Datatable
        $(document).ready(function() {
            $('#dataakun').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('admin.users.index') }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'gender',
                        name: 'gender'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },

                ],
                order: [
                    [0, 'asc']
                ],
                language: {
                    url: '{{ asset('vendor/datatables/dataTables.indonesia.json') }}'
                },
            });
        });
        // Delete Data
        $(document).on('click', '.delete', function() {
            dataId = $(this).attr('id');
            $('#deleteModal').modal('show');
        });

        $('#deleteData').click(function() {
            $.ajax({
                url: "users/" + dataId,
                type: 'DELETE',
                success: function(data) {
                    setTimeout(function() {
                        $('#deleteModal').modal('hide');
                        var oTable = $('#dataakun').dataTable();
                        oTable.fnDraw(false);
                    });
                    iziToast.warning({
                        title: 'Akun Berhasil Dihapus',
                        message: '{{ Session('delete') }}',
                        position: 'bottomRight'
                    });
                }
            })
        });
    </script>
@endsection
