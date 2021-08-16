<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>{site} ~ {coinname} faucet</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="/templates/standard/assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="/templates/standard/css/styles.css" rel="stylesheet" />
    </head>
    <body>
        <!-- Responsive navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="{url}">{site}</a>
            </div>
        </nav>
        <!-- Page header with logo and tagline-->
        <header class="py-5 bg-light border-bottom mb-4">
            <div class="container">
                <div class="text-center my-5">
                    <h1 class="fw-bolder">{coinname} faucet</h1>
                    <p class="lead mb-0">Get every {time} hour funds from our faucet</p>
                </div>
            </div>
        </header>
        <!-- Page content-->
        <div class="container">
            <div class="row">
                <!-- Blog entries-->
                <div class="col-lg-8">
                    <!-- Featured blog post-->
                    <div class="card mb-4">
						{ads}
                        <div class="card-body">
                            <h2 class="card-title">Claim {ticker}</h2>
                            <p class="card-text">You can get an reward between {minreward} and {maxreward} {ticker} per claim</p>
							{form}
						</div>
						{ads}
                    <div class="card-header">Latest payouts</div>
                        <div class="card-body">
							<table border="0" width="100%" class="table table-striped">
							<tr><td>Wallet address</td><td>{ticker}</td><td>Time</td></tr>
								{transactions}
							</table>
						</div>
					</div>
                    <!-- Pagination-->
                </div>
                <!-- Side widgets-->
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">Ads</div>
                        <div class="card-body">{ads}</div>
                    </div>
					<div class="card mb-4">
                        <div class="card-header">Faucet balance</div>
                        <div class="card-body"><b>{balance}</b> {ticker}<br />
						Please send us some coins to keep the faucet up and running to:<br />
						<b>{faucetwallet}</b></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer-->
        <footer class="py-5 bg-dark">
            <div class="container"><p class="m-0 text-center text-white">Copyright &copy; {site} {year}</p></div>
        </footer>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
		<script>
		var timeleft = {captchatimer};
		var downloadTimer = setInterval(function(){
		  if(timeleft <= 0){
			clearInterval(downloadTimer);
			document.getElementById("countdown").innerHTML = "<span style='color:white; font-weight:bold;'>{htmlcaptcha}</span>";
		  } else {
			document.getElementById("countdown").innerHTML = "Captcha is showing in " + timeleft + " seconds";
		  }
		  timeleft -= 1;
		}, 1000);
		</script>
    </body>
</html>
