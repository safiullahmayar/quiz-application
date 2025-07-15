@extends('layouts.app')
@section('title','Dashboard')
@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Admin</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Admin</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <!-- /.content-header -->

  <!-- Main content: Small Stat Boxes -->
                    @if($admin['is_admin'] == 1)

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <!-- Students Box -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h3>{{ $student }}</h3>
              <p>Total Std</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
          </div>
        </div>

        <!-- Admins Box -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3>{{ $admin->count() }}</h3>
              <p>Total admins</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
          </div>
        </div>

        <!-- Exams Box -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3>{{ $exam }}</h3>
              <p>Exams</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- /.Main stat box section -->
@endif
  <!-- Main content: Admin Table -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
                    @if($admin['is_admin'] == 1)

            <div class="card-header">
              <h3 class="card-title">Admins</h3>
              <div class="card-tools">
                <a class="btn btn-info btn-sm" href="javascript:;" data-toggle="modal" data-target="#myModal">Add new</a>
              </div>
            </div>
            @endif
            <div class="card-body">
              <table class="table table-striped table-bordered table-hover datatable">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                    @if($admin['is_admin'] == 1)
                    @foreach ($admins as $key => $std)
                    <tr>
                      <td>{{ $key + 1 }}</td>
                      <td>{{ $std['name'] }}</td>
                      <td>{{ $std['email'] }}</td>
                      <td>
                        {{-- <a href="{{ url('admin/edit_admins/'.$std['id']) }}" class="btn btn-primary btn-sm">Edit</a> --}}
                        <a href="javascript:void(0);" class="btn btn-danger btn-sm delete-admin" data-id="{{ $std['id'] }}">Delete</a>

                      </td>
                    </tr>
                    @endforeach
                    @else
                       <tr>
                      <td>1</td>
                      <td>{{ $admin['name'] }}</td>
                      <td>{{ $admin['email'] }}</td>
                      <td>
                        {{-- <a href="{{ url('admin/edit_admins/'.$admin['id']) }}" class="btn btn-primary btn-sm">Edit</a> --}}
                        <a href="javascript:void(0);" class="btn btn-danger btn-sm delete-admin" data-id="{{ $admin['id'] }}">Delete</a>

                      </td>
                    </tr>
                    @endif
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- /.Admin Table -->

</div>
<!-- /.content-wrapper -->

<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add new Admin</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <form action="{{ url('admin/add_new_admins') }}"  class="database_operation" id='addAdminForm'>
          {{ csrf_field() }}
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="">Enter Name</label>
                <input type="text" required name="name" placeholder="Enter name" class="form-control">
              </div>
            </div>

            <div class="col-sm-12">
              <div class="form-group">
                <label for="">Enter E-mail</label>
                <input type="text" required name="email" placeholder="Enter email" class="form-control">
              </div>
            </div>

            <div class="col-sm-12">
              <div class="form-group">
                <label for="">Password</label>
                <input type="password" required name="password" placeholder="Enter password" class="form-control">
              </div>
            </div>

            <div class="col-sm-12">
              <div class="form-group">
                <button class="btn btn-primary" type="submit">Add</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    $('#addAdminForm').submit(function (e) {
        e.preventDefault(); // prevent default form submit

        var form = $(this);
        var url = form.attr('action');

        // === FRONTEND VALIDATION ===
        let email = form.find('input[name="email"]').val();
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!email || !emailRegex.test(email)) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Email',
                text: 'Please enter a valid email address.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            return;
        }

        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(),
            success: function (response) {
                $('#myModal').modal('hide');

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: response.message || 'Admin added successfully!',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });

                setTimeout(function () {
                    location.reload();
                }, 1000);
            },
            error: function (xhr) {
                // Backend validation errors (Laravel)
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let firstError = Object.values(errors)[0][0];

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: firstError,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                } else {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: xhr.responseJSON?.message || 'An error occurred.',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
            }
        });
    });
});
</script>




<script>
$(document).ready(function() {
    $('.delete-admin').click(function(e) {
        e.preventDefault();

        let adminId = $(this).data('id');
        let url = `/admin/delete_admins/${adminId}`;
        let token = '{{ csrf_token() }}';
        let row = $(this).closest('tr'); // optional if you want to remove the row

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'post',
                    data: {
                        _token: token
                    },
                    success: function(response) {
                        Swal.fire(
                            'Deleted!',
                            response.message || 'Admin has been deleted.',
                            'success'
                        );
                        row.fadeOut(500, function() {
                            $(this).remove();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            'Failed to delete admin.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>


@endsection
