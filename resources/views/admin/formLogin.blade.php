<!DOCTYPE html>
<html lang="pt-br">

<head>
<meta charset="UTF-8">

<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">

<!------ Include the above in your HEAD tag ---------->

<style>
/* Credit to bootsnipp.com for the css for the color graph */
.colorgraph {
	height: 5px;
	border-top: 0;
	background: #c4e17f;
	border-radius: 5px;
	background-image: -webkit-linear-gradient(left, #c4e17f, #c4e17f 12.5%, #f7fdca 12.5%,
		#f7fdca 25%, #fecf71 25%, #fecf71 37.5%, #f0776c 37.5%, #f0776c 50%,
		#db9dbe 50%, #db9dbe 62.5%, #c49cde 62.5%, #c49cde 75%, #669ae1 75%,
		#669ae1 87.5%, #62c2e4 87.5%, #62c2e4);
	background-image: -moz-linear-gradient(left, #c4e17f, #c4e17f 12.5%, #f7fdca 12.5%,
		#f7fdca 25%, #fecf71 25%, #fecf71 37.5%, #f0776c 37.5%, #f0776c 50%,
		#db9dbe 50%, #db9dbe 62.5%, #c49cde 62.5%, #c49cde 75%, #669ae1 75%,
		#669ae1 87.5%, #62c2e4 87.5%, #62c2e4);
	background-image: -o-linear-gradient(left, #c4e17f, #c4e17f 12.5%, #f7fdca 12.5%, #f7fdca
		25%, #fecf71 25%, #fecf71 37.5%, #f0776c 37.5%, #f0776c 50%, #db9dbe
		50%, #db9dbe 62.5%, #c49cde 62.5%, #c49cde 75%, #669ae1 75%, #669ae1
		87.5%, #62c2e4 87.5%, #62c2e4);
	background-image: linear-gradient(to right, #c4e17f, #c4e17f 12.5%, #f7fdca 12.5%, #f7fdca
		25%, #fecf71 25%, #fecf71 37.5%, #f0776c 37.5%, #f0776c 50%, #db9dbe
		50%, #db9dbe 62.5%, #c49cde 62.5%, #c49cde 75%, #669ae1 75%, #669ae1
		87.5%, #62c2e4 87.5%, #62c2e4);
}
</style>

<script src="https://www.google.com/recaptcha/api.js"></script>

<script type="text/javascript">


$(function(){
    $('.button-checkbox').each(function(){
		var $widget = $(this),
			$button = $widget.find('button'),
			$checkbox = $widget.find('input:checkbox'),
			color = $button.data('color'),
			settings = {
					on: {
						icon: 'glyphicon glyphicon-check'
					},
					off: {
						icon: 'glyphicon glyphicon-unchecked'
					}
			};

		$button.on('click', function () {
			$checkbox.prop('checked', !$checkbox.is(':checked'));
			$checkbox.triggerHandler('change');
			updateDisplay();
		});

		$checkbox.on('change', function () {
			updateDisplay();
		});

		function updateDisplay() {
			var isChecked = $checkbox.is(':checked');
			// Set the button's state
			$button.data('state', (isChecked) ? "on" : "off");

			// Set the button's icon
			$button.find('.state-icon')
				.removeClass()
				.addClass('state-icon ' + settings[$button.data('state')].icon);

			// Update the button's color
			if (isChecked) {
				$button
					.removeClass('btn-default')
					.addClass('btn-' + color + ' active');
			}
			else
			{
				$button
					.removeClass('btn-' + color + ' active')
					.addClass('btn-default');
			}
		}
		function init() {
			updateDisplay();
			// Inject the icon if applicable
			if ($button.find('.state-icon').length == 0) {
				$button.prepend('<i class="state-icon ' + settings[$button.data('state')].icon + '"></i>�');
			}
		}
		init();
	});
});

function onSubmit(token) {
   // alert(token);
    document.getElementById("demo-form").submit();
}


</script>
</head>
<div class="container">
	<div class="row" style="margin-top: 20px">
		<div
			class="col-xs-8 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
			<form role="form" action="{{route('admin.login.do')}}" method="post" id="demo-form" name="demo-form">
				@csrf
				<fieldset>
					<h2>Painel Adminstrativo</h2>
					<hr class="colorgraph">
					<div class="form-group">
						<input type="text" name="email" id="email" class="form-control input-lg" placeholder="Email">
					</div>
					<div class="form-group">
						<input type="password" name="password" id="password" class="form-control input-lg" placeholder="Password">
					</div>
					<hr class="colorgraph">

					@if($errors->all())
						@foreach($errors->all() as $error)
							<div class="alert alert-danger" role="alert">
                              {{ $error }}
                            </div>
						@endforeach
					@endif

					<div class="row">
                        <div class="text-center" >
                            <input type="button" class="btn btn-lg btn-success btn-block g-recaptcha" data-sitekey="{{ env('DATA_SITEKEY') }} " data-callback='onSubmit'  value="Logar">
                        </div>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
</div>

</html>
