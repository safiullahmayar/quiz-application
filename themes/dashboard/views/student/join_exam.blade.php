@extends('layouts.student')

@section('title','Exams')

@section('content')
<style type="text/css">
    .question_options>li{
        list-style: none;
        height: 40px;
        line-height: 40px;
    }
    #warning-message {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        background: #ff0000;
        color: white;
        text-align: center;
        padding: 15px;
        z-index: 9999;
    }
    #fullscreen-warning {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.9);
        color: white;
        text-align: center;
        padding-top: 20%;
        z-index: 10000;
    }
    #negative-mark-notification {
        position: fixed;
        top: 50px;
        left: 0;
        width: 100%;
        background-color: #ff0000;
        color: white;
        text-align: center;
        padding: 15px;
        z-index: 9999;
        display: none;
    }
</style>

<!-- Warning messages -->
<div id="warning-message">
    Warning: You have switched tabs/windows. Please return to the quiz immediately or it will be automatically submitted!
</div>
<div id="fullscreen-warning">
    <h2>Fullscreen mode is required for this exam</h2>
    <button id="enter-fullscreen" class="btn btn-primary btn-lg">Enter Fullscreen</button>
</div>
<div id="negative-mark-notification">
    <strong>Warning:</strong> 5 marks have been deducted for leaving the exam window!
</div>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Exams</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Exam</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->

      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <!-- Default box -->
              <div class="card">
                <div class="card-body">
                   <div class="row">
                       <div class="col-sm-4">
                          <h3 class="text-center">Time : {{ $exam->exam_duration}} min</h3>
                       </div>
                       <div class="col-sm-4">
                           <h3><b>Timer</b> :  <span class="js-timeout" id="timer">{{ $exam['exam_duration']}}:00</span></h3>
                       </div>
                       <div class="col-sm-4">
                            <h3 class="text-right"><b>Status</b> :Running</h3>
                        </div>
                   </div>
                </div>
              </div>

              <div class="card mt-4">
                <div class="card-body">
                  <form action="{{url('student/submit_questions')}}" method="POST" id="quizForm">
                    <input type="hidden" name="exam_id" value="{{ Request::segment(3)}}">
                    {{ csrf_field()}}
                    <input type="hidden" id="exam_id" value="{{ Request::segment(3) }}">

                    <div class="row">
                        @php $questionCount = 0; @endphp
                        @foreach ($question as $key=>$q)
                            @php $questionCount++; @endphp
                            <div class="col-sm-12 mt-4">
                              <p>{{$key+1}}. {{ $q->questions}}</p>
                              <?php
                                    $options = json_decode(json_decode(json_encode($q->options)),true);
                              ?>
                              <input type="hidden" name="question{{$key+1}}" value="{{$q['id']}}">
                              <ul class="question_options">
                                  <li><input type="radio" value="{{ $options['option1']}}" name="ans{{$key+1}}"> {{ $options['option1']}}</li>
                                  <li><input type="radio" value="{{ $options['option2']}}" name="ans{{$key+1}}"> {{ $options['option2']}}</li>
                                  <li><input type="radio" value="{{ $options['option3']}}" name="ans{{$key+1}}"> {{ $options['option3']}}</li>
                                  <li><input type="radio" value="{{ $options['option4']}}" name="ans{{$key+1}}"> {{ $options['option4']}}</li>
                                  <li style="display: none;"><input value="0" type="radio" checked="checked" name="ans{{$key+1}}"> {{ $options['option4']}}</li>
                              </ul>
                            </div>
                        @endforeach

                        <div class="col-sm-12">
                            <input type="hidden" name="index" value="{{ $questionCount }}">
                            <button type="submit" class="btn btn-primary" id="myCheck">Submit</button>
                        </div>
                   </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
</div>
<script>
// Global variables
let tabChanged = false;
let warningShown = false;
let submitTimeout;
let firstTabSwitch = true; // Track first tab switch
let firstFullscreenExit = true; // Track first fullscreen exit
let examId = document.getElementById('exam_id').value;
let userId = {{ auth()->user()->id }};
let quizToken = 'quiz_' + userId + '_' + examId + '_' + Math.random().toString(36).substring(2, 15);
let examStarted = false; // Track if exam has started

// Initialize fullscreen requirement
document.addEventListener('DOMContentLoaded', function() {
    // Set exam as started after a small delay
    setTimeout(() => {
        examStarted = true;
    }, 1000);

    // Check if fullscreen is required
    if (!document.fullscreenElement) {
        document.getElementById('fullscreen-warning').style.display = 'block';
    }

    // Initialize session storage
    sessionStorage.setItem('quizToken', quizToken);
    sessionStorage.setItem('quizActive', 'true');

    // Start heartbeat
    startHeartbeat();

    // Add fullscreen change listener
    document.addEventListener('fullscreenchange', handleFullscreenChange);
});

// Handle fullscreen change events
function handleFullscreenChange() {
    // Only act if exam has started
    if (!examStarted) return;

    if (!document.fullscreenElement) {
        // User exited fullscreen
        handleFullscreenExit();
    } else {
        // User entered fullscreen - reset first exit flag
        firstFullscreenExit = true;
        document.getElementById('fullscreen-warning').style.display = 'none';
        clearTimeout(submitTimeout);
    }
}

function handleFullscreenExit() {
    // Only act if exam has started
    if (!examStarted) return;

    // Show fullscreen warning
    document.getElementById('fullscreen-warning').style.display = 'block';

    // First time: deduct marks
    if (firstFullscreenExit) {
        firstFullscreenExit = false;
        deductMarks();

        // Set timeout for submission if they don't return
        submitTimeout = setTimeout(function() {
            submitQuiz('fullscreen_exit_timeout');
        }, 3000); // 3 seconds grace period
    }
    // Second time: submit immediately
    else {
        clearTimeout(submitTimeout); // Clear any existing timeout
        submitQuiz('second_fullscreen_exit');
    }
}

// Function to submit the form
function submitQuiz(reason) {
    // Only submit if exam has started
    if (!examStarted) return;

    let input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'auto_submitted';
    input.value = reason || 'tab_switch';
    document.getElementById('quizForm').appendChild(input);
    document.getElementById('quizForm').submit();
}
// Fullscreen handler with better browser support
document.getElementById('enter-fullscreen').addEventListener('click', function() {
    // Try to enter fullscreen mode
    const element = document.documentElement;

    // Standard method (most browsers)
    if (element.requestFullscreen) {
        element.requestFullscreen().then(() => {
            // Success - hide the warning
            document.getElementById('fullscreen-warning').style.display = 'none';
        }).catch(err => {
            // Failure - show error
            alert("Could not enable fullscreen: " + err.message);
            console.error("Fullscreen error:", err);
        });
    }
    // WebKit (Safari) method
    else if (element.webkitRequestFullscreen) {
        element.webkitRequestFullscreen();
        document.getElementById('fullscreen-warning').style.display = 'none';
    }
    // Firefox method
    else if (element.mozRequestFullScreen) {
        element.mozRequestFullScreen();
        document.getElementById('fullscreen-warning').style.display = 'none';
    }
    // IE/Edge method
    else if (element.msRequestFullscreen) {
        element.msRequestFullscreen();
        document.getElementById('fullscreen-warning').style.display = 'none';
    }
    else {
        alert("Fullscreen is not supported by your browser");
    }
});

// Enhanced fullscreen change detection
function handleFullscreenChange() {
    // Check all possible fullscreen states
    const isFullscreen = document.fullscreenElement ||
                        document.webkitFullscreenElement ||
                        document.mozFullScreenElement ||
                        document.msFullscreenElement;

    if (!isFullscreen) {
        // User exited fullscreen
        handleFullscreenExit();
    } else {
        // User entered fullscreen - hide warning and clear timeout
        document.getElementById('fullscreen-warning').style.display = 'none';
        clearTimeout(submitTimeout);
    }
}

// Add event listeners for all browser variants
document.addEventListener('fullscreenchange', handleFullscreenChange);
document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
document.addEventListener('mozfullscreenchange', handleFullscreenChange);
document.addEventListener('MSFullscreenChange', handleFullscreenChange);
// Function to deduct marks
function deductMarks() {
    // Add hidden input to form for mark deduction
    let input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'negative_mark';
    input.value = '5';
    document.getElementById('quizForm').appendChild(input);

    // Show notification
    document.getElementById('negative-mark-notification').style.display = 'block';
    setTimeout(() => {
        document.getElementById('negative-mark-notification').style.display = 'none';
    }, 5000);
}
</script>
