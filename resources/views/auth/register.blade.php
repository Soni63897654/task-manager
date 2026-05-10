@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm mt-5">
            <div class="card-body p-4">
                
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Register</h2>
                    <p class="text-muted">Create your account</p>
                </div>

                <form id="registerForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter your name">
                        <p id="nameError" class="error-msg text-danger small mt-1"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="name@example.com">
                        <p id="emailError" class="error-msg text-danger small mt-1"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Min 6 characters">
                        <p id="passwordError" class="error-msg text-danger small mt-1"></p>
                    </div>

                    <div id="responseMessage" class="alert d-none mt-3" role="alert"></div>

                    <div class="d-grid mt-4">
                        <button type="submit" id="regBtn" class="btn btn-dark py-2 shadow-sm">
                            Register
                        </button>
                    </div>

                </form>

                <p class="text-center mt-4 mb-0 small">
                    Already have an account? 
                    <a href="/login" class="text-success text-decoration-none fw-bold">Login</a>
                </p>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function () {
    // CSRF Token setup for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').val()
        }
    });

    $("#registerForm").validate({
        rules: {
            name: { required: true, minlength: 3 },
            email: { required: true, email: true },
            password: { required: true, minlength: 6 }
        },
        messages: {
            name: { required: "Please enter your name", minlength: "Min 3 characters required" },
            email: { required: "Email is required", email: "Invalid email format" },
            password: { required: "Password is required", minlength: "Min 6 characters required" }
        },
        errorPlacement: function(error, element) {
            let errorId = element.attr('name') + "Error";
            $("#" + errorId).text(error.text());
        },
        highlight: function(element) {
            $(element).addClass('is-invalid').removeClass('is-valid');
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid').addClass('is-valid');
            let errorId = $(element).attr('name') + "Error";
            $("#" + errorId).text("");
        },

        submitHandler: function(form) {
            let btn = $("#regBtn");
            let msgBox = $("#responseMessage");

            // UI Reset
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Creating Account...');
            msgBox.addClass('d-none').stop(true, true).show().removeClass('alert-success alert-danger').text("");

            $.ajax({
                url: "/register",
                type: "POST",
                data: $(form).serialize(),
                dataType: "json",
                success: function(response) {
                    if(response.success) {
                        msgBox.removeClass('d-none').addClass('alert-success').text(response.message);
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1500);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Register');
                    
                    let errorText = "Something went wrong!";
                    
                    if(xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        errorText = Object.values(errors)[0][0]; 
                    }
                    msgBox.removeClass('d-none').addClass('alert-danger').text(errorText);

                    // --- AUTO HIDE ERROR LOGIC ---
                    setTimeout(function() {
                        msgBox.fadeOut(600, function() {
                            $(this).addClass('d-none').css('display', ''); 
                        });
                    }, 4000); 
                }
            });
            return false; 
        }
    });
});
</script>
@endpush
@endsection