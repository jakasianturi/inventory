@extends('dashboard.layouts.app')
@section('title', 'Daftar Kategori Produk')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Daftar Kategori Produk</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Kategori</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container">
                <div class="d-sm-flex align-items-center justify-content-start mb-4">
                    @if(auth()->user()->role == 'admin')
                        <a href="{{ route('dashboard.categories.create') }}" class="d-sm-inline-block mt-mw-sm-2 btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-plus fa-sm mr-1"></i> Tambah Kategori
                        </a>
                    @endif
                </div>

                @if (session('message'))
                    <div class="alert alert-success alert-dismissible fade show mb-4 border-left-success" role="alert">
                        {{ session('message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4 border-left-danger" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card card-primary card-outline mb-4">
                    <div class="card-body">
                        <table class="table table-bordered table-striped" id="datakategori" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 5%">No</th>
                                    <th scope="col" style="width: 25%">Nama Kategori</th>
                                    <th scope="col" style="width: 35%">Deskripsi</th>
                                    <th scope="col" style="width: 15%">Total Produk</th> 
                                    <th scope="col" class="text-center" style="width: 20%">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel">Hapus Kategori</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Apakah Anda yakin ingin menghapus kategori ini? Kategori tidak dapat dihapus jika masih ada produk yang terkait di dalamnya.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button id="deleteData" type="button" class="btn btn-danger">Hapus Kategori</button>
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
        let dataId;

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Inisialisasi DataTables Kategori
            $('#datakategori').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('dashboard.categories.index') }}",
                    type: 'GET'
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'description', name: 'description', defaultContent: '-' },
                    { data: 'total_products', name: 'products_count', searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[1, 'asc']],
            });
        });

        // Trigger Modal Hapus
        $(document).on('click', '.delete', function() {
            dataId = $(this).attr('id');
            $('#deleteModal').modal('show');
        });

        // Eksekusi Hapus AJAX
        $('#deleteData').click(function() {
            let baseUrl = "{{ url('dashboard/categories') }}";
            
            $.ajax({
                url: baseUrl + '/' + dataId,
                type: 'DELETE',
                success: function(data) {
                    setTimeout(function() {
                        $('#deleteModal').modal('hide');
                        let oTable = $('#datakategori').dataTable();
                        oTable.fnDraw(false); 
                    });
                    
                    iziToast.warning({
                        title: 'Berhasil',
                        message: 'Data kategori berhasil dihapus',
                        position: 'bottomRight'
                    });
                },
                error: function(xhr) {
                    $('#deleteModal').modal('hide');
                    iziToast.error({
                        title: 'Gagal Menghapus',
                        message: xhr.responseJSON.message || 'Kategori ini mungkin sedang digunakan oleh produk.',
                        position: 'bottomRight'
                    });
                }
            });
        });
    </script>
@endsection