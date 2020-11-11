<?php 

	//Consultar servicios API
	function get_api($url){
		$url_btc = $url;
  		$json_btc = file_get_contents($url_btc);
  		$btc = json_decode($json_btc,true);
		return $btc;
	}


	//Notificar por email
	function send_email($email, $cotizacion){
		//para el envío en formato HTML 
		$headers = "MIME-Version: 1.0\r\n"; 
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
		//dirección del remitente 
		$headers .= "From: kaltre10@gmail.com\r\n"; 
		//dirección de respuesta
		$headers .= "Reply-To: kaltre10@gmail.com\r\n"; 
		//ruta del mensaje desde origen a destino 
		$headers .= "Return-path: kaltre10@gmail.com\r\n"; 
		//direcciones que recibián copia 
		$headers .= "Cc: kaltre10@gmail.com\r\n"; 
		//direcciones que recibirán copia oculta 
		$headers .= "Bcc: kaltre10@gmail.com\r\n"; 
		$asunto = "Oportunidad para comprar bitcoins en localbitcoins";
		$cuerpo = "Precio disponible a $cotizacion";
		mail($email,$asunto,$cuerpo,$headers);
	}

	$bitfinex_precio_btc = get_api('https://api.bitfinex.com/v1/pubticker/btcusd');

	$ewforex = get_api('https://ewforex.net/app/api');

	$localbitcoins_compras = get_api('https://localbitcoins.com/buy-bitcoins-online/PEN/transfers-with-specific-bank/.json');

	$localbitcoins_ventas = get_api('https://localbitcoins.com/sell-bitcoins-online/PEN/transfers-with-specific-bank/.json');

 ?>
 <!DOCTYPE html>
 <html lang="es">
 <head>
 	<title>Cotizacion de BTC</title>
 	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
 	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
 	<link rel="stylesheet" type="text/css" href="style.css">
 </head>
 <body>
 	<div class="container mt-1">
 		<div class="row card title-bitfinex p-3">
		  <h1 class="display-3">Bitfinex: <?= $bitfinex_precio_btc["ask"]; ?></h1>
		</div>
		<div class="row" style="box-sizing: border-box;">
			<div class="col-12 col-md-6">
				<h3>Compras</h3>
				<table class="table table-striped">
				  <thead>
				    <tr>
				      <th scope="col">Precio</th>
				      <th scope="col">Tipo de cambio</th>
				      <th scope="col">Usuario</th>
				      <th scope="col">Min</th>
				      <th scope="col">Max</th>
				      <th scope="col"></th>
				    </tr>
				  </thead>
				  <tbody>
				  	<?php 

				  		$contador = 0;

				  		foreach ($localbitcoins_compras["data"]["ad_list"] as $key) :

			  			if ($contador == 5) {
						    break;  
						}

			  			$reputacion = $key['data']['profile']['feedback_score'];
						$cantidad_max = $key['data']['max_amount'];
						$cantidad_min = $key['data']['min_amount'];

					if ($reputacion == 100 && $cantidad_max > 2000) : ?>
					    <tr>
					        <th scope="row"><?= $key['data']['temp_price']; ?></th>

					        <?php 
					        	$cotizacion = round($key['data']['temp_price'] / $bitfinex_precio_btc["ask"], 2);

					      		$cambio = $ewforex[0]['com_divisa'] = $ewforex[0]['com_divisa'] + 0.01;
					        ?>

					        <td><?= $cotizacion; ?></td>
					        <?php 

					      		if ($cotizacion < $cambio) {
					      			send_email("kaltre10@gmail.com", $cotizacion);
					      		}

					        ?>

					        <td><?= $key['data']['profile']['username']; ?></td>
					        <td><?= $cantidad_min; ?></td>
					        <td><?= $cantidad_max; ?></td>
					        <td><a href="<?= $key['actions']['public_view']; ?>" target='_blanck'> ir </a></td>
					    </tr>
					<?php 

						$contador++;
						endif;
						endforeach; 

					?>
				  </tbody>
				</table>
			</div>
			<div class="col-12 col-md-6">
				<h3>Ventas</h3>
				<div class="table-responsive">
					<table class="table table-striped">
					  <thead>
					    <tr>
					      <th scope="col">Precio</th>
					      <th scope="col">Tipo de cambio</th>
					      <th scope="col">Usuario</th>
					      <th scope="col">Min</th>
					      <th scope="col">Max</th>
					      <th scope="col"></th>
					    </tr>
					  </thead>
					  <tbody>
					  	<?php 

					  		$contador = 0;
					  		foreach ($localbitcoins_ventas["data"]["ad_list"] as $key) :
						  		if ($contador == 5) {
									break;
								} 

				  			$reputacion = $key['data']['profile']['feedback_score'];
							$cantidad_max = $key['data']['max_amount'];
							$cantidad_min = $key['data']['min_amount'];
						
							if ($reputacion == 100 && $cantidad_max > 2000) :

						?>
						    <tr>
						      <th scope="row"><?= $key['data']['temp_price']; ?></th>
						      <td><?= round($key['data']['temp_price'] / $bitfinex_precio_btc["ask"], 2); ?></td>
						      <td><?= $key['data']['profile']['username']; ?></td>
						      <td><?= $cantidad_min; ?></td>
						      <td><?= $cantidad_max; ?></td>
						      <td><a href="<?= $key['actions']['public_view']; ?>" target='_blanck'> ir </a></td>
						    </tr>
						<?php

							$contador++; 
							endif;
							endforeach; 
							
						?>
					  </tbody>
					</table>
				</div>
			</div>
		</div>
 	</div>
 </body>
 </html>