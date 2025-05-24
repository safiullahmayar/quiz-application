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
</style>

<!-- Warning messages -->
<div id="warning-message">
    Warning: You have switched tabs/windows. Please return to the quiz immediately or it will be automatically submitted!
</div>
<div id="fullscreen-warning">
    <h2>Fullscreen mode is required for this exam</h2>
    <button id="enter-fullscreen" class="btn btn-primary btn-lg">Enter Fullscreen</button>
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
                        @foreach ($question as $key=>$q)
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
                            <input type="hidden" name="index" value="{{ $key+1}}">
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
let examId = document.getElementById('exam_id').value;
let userId = {{ auth()->user()->id }};
let quizToken = 'quiz_' + userId + '_' + examId + '_' + Math.random().toString(36).substring(2, 15);

// Initialize fullscreen requirement
document.addEventListener('DOMContentLoaded', function() {
    // Check if fullscreen is required
    if (!document.fullscreenElement) {
        document.getElementById('fullscreen-warning').style.display = 'block';
    }

    // Initialize session storage
    sessionStorage.setItem('quizToken', quizToken);
    sessionStorage.setItem('quizActive', 'true');

    // Start heartbeat
    startHeartbeat();
});

// Fullscreen handler
document.getElementById('enter-fullscreen').addEventListener('click', function() {
    document.documentElement.requestFullscreen().then(() => {
        document.getElementById('fullscreen-warning').style.display = 'none';
    }).catch(err => {
        alert('Fullscreen error: ' + err.message);
    });
});

// Function to submit the form
function submitQuiz(reason) {
    // Add a hidden field to indicate auto-submission
    let input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'auto_submitted';
    input.value = reason || 'tab_switch';
    document.getElementById('quizForm').appendChild(input);

    // Submit the form
    document.getElementById('quizForm').submit();
}

// Visibility change detection
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'hidden') {
        handleTabSwitch();
    } else if (warningShown) {
        // User returned to the tab
        document.getElementById('warning-message').style.display = 'none';
        clearTimeout(submitTimeout);
    }
});

// Window blur detection (for older browsers)
window.addEventListener('blur', function() {
    handleTabSwitch();
});

function handleTabSwitch() {
    // User switched tabs or minimized window
    tabChanged = true;
    document.getElementById('warning-message').style.display = 'block';
    warningShown = true;

    // Submit immediately or after delay
    submitTimeout = setTimeout(function() {
        submitQuiz('tab_switch_timeout');
    }, 3000); // 3 seconds grace period
}

// Prevent keyboard shortcuts for new tabs/windows
document.addEventListener('keydown', function(e) {
    // Ctrl/Command + T, N, Tab, etc.
    if ((e.ctrlKey || e.metaKey) &&
        (e.key === 't' || e.key === 'T' ||
         e.key === 'n' || e.key === 'N' ||
         e.key === 'Tab')) {
        e.preventDefault();
        alert('Opening new tabs/windows is not allowed during the exam.');
    }

    // F11 for fullscreen toggling (optional)
    if (e.key === 'F11') {
        e.preventDefault();
    }
});

// Prevent right-click
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
    alert('Right-click is disabled during the exam.');
});

// Prevent copy/paste
['copy', 'cut', 'paste'].forEach(function(event) {
    document.addEventListener(event, function(e) {
        e.preventDefault();
        alert('This action is not allowed during the exam.');
    });
});

// Detect if this is a duplicate tab
window.addEventListener('load', function() {
    if (sessionStorage.getItem('quizActive') === 'true' &&
        sessionStorage.getItem('quizToken') !== quizToken) {
        // This is a duplicate tab - submit immediately
        submitQuiz('duplicate_tab');
    }
});

// Clear the flag when leaving normally
window.addEventListener('beforeunload', function() {
    if (!tabChanged) {
        sessionStorage.removeItem('quizActive');
    }
});

// Heartbeat to server to detect multiple tabs
function startHeartbeat() {
    // Initial heartbeat
    sendHeartbeat();

    // Regular heartbeat every 2 seconds
    setInterval(sendHeartbeat, 2000);
}

function sendHeartbeat() {
    fetch('/quiz/heartbeat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            token: quizToken,
            exam_id: examId,
            user_id: userId
        })
    }).then(response => {
        if (!response.ok) throw new Error('Network error');
        return response.json();
    }).then(data => {
        if (data.status === 'duplicate') {
            submitQuiz('server_duplicate_detected');
        }
    }).catch(error => {
        console.error('Heartbeat error:', error);
    });
}
</script>
@endsection
