@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm mt-5">
            <div class="card-body p-4">
                
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Login</h2>
                    <p class="text-muted">Sign in to your account</p>
                </div>

                <form id="loginForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="example@mail.com">
                        <p id="emailError" class="text-danger small mt-1"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Enter password">
                        <p id="passwordError" class="text-danger small mt-1"></p>
                    </div>

                    <div id="generalError" class="alert alert-danger d-none mt-3 py-2" role="alert"></div>

                    <div class="d-grid mt-4">
                        <button type="submit" id="loginBtn" class="btn btn-dark py-2 shadow-sm">
                            Login
                        </button>
                    </div>

                </form>

                <p class="text-center mt-4 mb-0 small">
                    Don't have an account? 
                    <a href="/register" class="text-success text-decoration-none fw-bold">Register</a>
                </p>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function () {
    // Ajax Setup for CSRF
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() }
    });

    $("#loginForm").validate({
        // 1. Validation Rules
        rules: {
            email: { required: true, email: true },
            password: { required: true, minlength: 6 }
        },
        // 2. Custom Messages
        messages: {
            email: { required: "Please enter email", email: "Invalid email format" },
            password: { required: "Please enter password", minlength: "Min 6 chars required" }
        },
        // 3. Error Placement
        errorPlacement: function(error, element) {
            let errorId = element.attr('name') + "Error";
            $("#" + errorId).text(error.text());
        },
        // 4. UI Feedback
        highlight: function(element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid');
            let errorId = $(element).attr('name') + "Error";
            $("#" + errorId).text("");
        },

        // 5. AJAX Submit Logic
        submitHandler: function(form) {
            let btn = $("#loginBtn");
            let genError = $("#generalError");

            // Reset UI states
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Checking...');
            genError.addClass('d-none').stop(true, true).show().text(""); // Reset alert box
            $(".text-danger").text(""); 

            $.ajax({
                url: "/login",
                type: "POST",
                data: $(form).serialize(),
                success: function (response) {
                    if (response.success) {
                        window.location.href = response.redirect;
                    }
                },
                error: function (xhr) {
                    btn.prop('disabled', false).text('Login');

                    let errorMsg = "An unexpected error occurred.";

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.email) $("#emailError").text(errors.email[0]);
                        if (errors.password) $("#passwordError").text(errors.password[0]);
                        return;
                    } 
                    else if (xhr.status === 401 || xhr.status === 404) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    genError.removeClass('d-none').text(errorMsg);

                    // --- AUTO HIDE LOGIC ---
                    setTimeout(function() {
                        genError.fadeOut(600, function() {
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