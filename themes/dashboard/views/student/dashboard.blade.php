@extends('layouts.student')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    .btn-whatsapp-pulse {
        background: #25d366;
        color: white;
        position: fixed;
        bottom: 10rem;
        right: 11px;
        z-index: 1212121212121221;
        font-size: 33px;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 0;
        height: 0;
        padding: 26px;
        text-decoration: none;
        border-radius: 50%;
        animation-name: pulse;
        animation-duration: 1.5s;
        animation-timing-function: ease-out;
        animation-iteration-count: infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.5);
        }

        80% {
            box-shadow: 0 0 0 14px rgba(37, 211, 102, 0);
        }
    }

    .btn-whatsapp-pulse-border {
        bottom: 120px;
        right: 20px;
        animation-play-state: paused;
    }

    @media only screen and (max-width: 600px) {
        .support {
            margin-top: -61px;
        }
        .mobile-menu
        {
            margin-top: -41px;
        }

        .logo a img {
                    width: 61px;
            margin-left: -36px;
             margin-top: -2px;
}

          .header-three .mean-container a.meanmenu-reveal {
            color: #000;
            border: 1px solid #000;
            margin-top: -16px;
        }
    }


    /* Small devices (portrait tablets and large phones, 600px and up) */
    @media only screen and (min-width: 600px) {
        .show-mobile {


        }

        .logo {
            width: 100%;
            height: 100%;
        }

        .logo a img {
                    width: 61px;
            margin-top:-2px;
            margin-left: -36px;
        }
    }

    /* Medium devices (landscape tablets, 768px and up) */
    @media only screen and (min-width: 768px) {
        .show-mobile {
            top: 0%;

        }

        .logo a img {
        width: 103px;
            margin-left: -36px;
        }
          .header-three .mean-container a.meanmenu-reveal {
            color: #000;
            border: 1px solid #000;
            margin-top: -16px;
        }
    }

</style>
@section('title','Portal dashboard')
@section('content')

     <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">All exams</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->

        <div class="row">


          @foreach ($portal_exams as $key=>$exam)
          <?php

              if(strtotime(date('Y-m-d')) > strtotime($exam['exam_date']))
              {
                  $cls="bg-danger";
              }
              else
              {
                  $val=$key+1;
                  if($val%2==0)
                      $cls="bg-info";
                  else
                      $cls="bg-success";
              }

          ?>

          <div class="col-lg-6 col-6">
              <div class="small-box <?php echo $cls; ?>">
                  <div class="inner">
                  <h3>{{ $exam['title']}}</h3>

                  <p>{{ $exam['cat_name']}}</p>
                  <p>Exam date : {{$exam['exam_date']}}</p>
                  </div>
                  <div class="icon">
                  <i class="ion ion-bag"></i>
                  </div>
                  @if (strtotime(date('Y-m-d')) <= strtotime($exam['exam_date']))

                      <a data-id="{{ $exam['id']}}"  class="apply_exam small-box-footer">Apply<i class="fas fa-arrow-circle-right"></i></a>

                  @endif

              </div>
          </div>
      @endforeach

        </div>
        <!-- /.row -->
        <!-- Main row -->

        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
     <a href="https://api.whatsapp.com/send?phone=03443998332&amp;text=" class="btn-whatsapp-pulse" target="_blank">
        <i class="fa-brands fa-whatsapp"></i>
    </a>
@endsection
