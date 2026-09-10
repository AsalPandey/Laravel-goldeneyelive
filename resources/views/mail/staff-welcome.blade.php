<p>Hello {{ $user->name }},</p>

<p>A Staff account has been created for you in the Golden Eye Academy CMS.</p>

<p>Account email:<br>{{ $user->email }}</p>

<p>To set your password for the first time:</p>

<ol>
    <li>Open the Forgot Password page.</li>
    <li>Enter your Golden Eye Academy email address.</li>
    <li>Enter the 6-digit OTP sent to your email.</li>
    <li>Create your new password.</li>
</ol>

<p>Set your password:<br><a href="{{ route('password.request') }}">{{ route('password.request') }}</a></p>

<p>After setting your password, log in here:<br><a href="{{ route('login') }}">{{ route('login') }}</a></p>

<p>For security, no temporary password has been sent by email.</p>

<p>Golden Eye Academy</p>
