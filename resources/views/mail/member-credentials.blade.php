<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Member Credentials</title>
	<style>
		body{font-family:Arial,Helvetica,sans-serif;background:#f6f6f6;padding:20px;color:#333}
		.container{max-width:600px;margin:0 auto;background:#ffffff;padding:20px;border-radius:6px}
		.btn{display:inline-block;padding:10px 16px;background:#007bff;color:#fff;text-decoration:none;border-radius:4px}
		.footer{font-size:12px;color:#666;margin-top:20px}
	</style>
</head>
<body>
	<div class="container">
		<h2>Welcome {{ ($member->full_name ?? $member_name ?? '') }}</h2>
		<p>Your account has been created. Use the credentials below to sign in to your member portal.</p>

		<p><strong>Email:</strong> {{ ($member->email ?? $member_email ?? '') }}</p>
		<p><strong>Password:</strong> {{ ($password ?? '—') }}</p>

		<p>
			<a class="btn" href="{{ url('http://gym-system.test/login') }}">Sign in to your account</a>
		</p>

		@if(isset($temporary) && $temporary)
			<p style="color:#a00">This is a temporary password. Please change it after signing in.</p>
		@endif

		<p class="footer">If you did not request this account, please ignore this email or contact support.</p>
	</div>
</body>
</html>
