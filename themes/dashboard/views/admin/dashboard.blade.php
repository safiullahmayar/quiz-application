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

  <!-- Main content: Admin Table -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Admins</h3>
              <div class="card-tools">
                <a class="btn btn-info btn-sm" href="javascript:;" data-toggle="modal" data-target="#myModal">Add new</a>
              </div>
            </div>
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
                  @foreach ($admin as $key => $std)
                    <tr>
                      <td>{{ $key + 1 }}</td>
                      <td>{{ $std['name'] }}</td>
                      <td>{{ $std['email'] }}</td>
                      <td>
                        <a href="{{ url('admin/edit_admins/'.$std['id']) }}" class="btn btn-primary btn-sm">Edit</a>
                        <a href="{{ url('admin/delete_admins/'.$std['id']) }}" class="btn btn-danger btn-sm">Delete</a>
                      </td>
                    </tr>
                  @endforeach
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
<script>
$(document).ready(function () {
    $('#addAdminForm').submit(function (e) {
        e.preventDefault(); // prevent normal form submit

        var form = $(this);
        var url = form.attr('action');

        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(),
            success: function (response) {
                // Close modal
                $('#myModal').modal('hide');

                // Optionally show success alert
                alert('Admin added successfully!');

                // Optionally reload table or page
                location.reload();
            },
            error: function (xhr) {
                // Handle validation errors
                alert('Error: ' + xhr.responseJSON.message);
            }
        });
    });
});
</script>
@endsection
