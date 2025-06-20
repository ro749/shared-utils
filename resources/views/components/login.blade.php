<form id="login-form">
    @csrf
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    @if($remember)
    <label>
        <input type="checkbox" name="remember"> Remember Me
    </label>
    @endif
    <button type="submit">Login</button>
    <div id="login-error" style="color: red; display: none;"></div>
</form>
@push('scripts')
<script>
$('#login-form').on('submit', function (e) {
    e.preventDefault();

    const form = $(this);
    const data = form.serialize();

    $.ajax({
        url: '/login',
        method: 'POST',
        data: data,
        success: function (response) {
            console.log(response);
            //window.location.href = response.redirect || '/';
        },
        error: function (xhr) {
            console.log(xhr);
            //const err = xhr.responseJSON?.message || 'Invalid credentials.';
            //$('#login-error').text(err).show();
        }
    });
});
</script>
@endpush