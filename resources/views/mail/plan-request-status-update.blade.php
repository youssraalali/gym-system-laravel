<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Plan Request Status Update</title>
	<style>
		body{font-family:Arial,Helvetica,sans-serif;background:#f6f6f6;padding:20px;color:#333}
		.container{max-width:600px;margin:0 auto;background:#ffffff;padding:20px;border-radius:6px}
		.btn{display:inline-block;padding:10px 16px;background:#007bff;color:#fff;text-decoration:none;border-radius:4px}
		.footer{font-size:12px;color:#666;margin-top:20px}
	</style>
</head>
<body>
	<div class="container">
		<h2>Welcome {{ ($planRequest->member->full_name ?? $planRequest->member_name ?? '') }}</h2>
		<p>Your plan request status has been updated to {{ $planRequest->status }}. Please log in to your account to view the details.</p>

		<p>
			<a class="btn" href="{{ url('http://gym-system.test/login') }}">Sign in to your account</a>
		</p>

		<p class="footer">If you did not request this plan, please contact support.</p>
	</div>
</body>
</html>
