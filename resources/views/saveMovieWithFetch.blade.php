<!DOCTYPE html>
<html lang="es" dir="ltr">

<head>
	<meta charset="utf-8">
	{{-- Necesario para poder enviar DATA vía fetch --}}
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>Fetch Laravel</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
	<link rel="stylesheet" href="/css/app.css">
	<link rel="stylesheet" href="/css/main.css">
</head>

<body>
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-8">
				<h2>Total de películas: <span>(aqui va el # de pelis)</span></h2>
				<!-- En este listado se cargarán las películas que vengan de la consulta asíncrona -->
				<ul class="list-group"></ul>
			</div>
			<div class="col-4">
				<h2>Dar de alta una película</h2>
				<form method="post" enctype="multipart/form-data">
					@csrf
					<input type="hidden" name="id" value="">
					<div class="form-group">
						<label>Title:</label>
						<input type="text" name="title" class="form-control">
					</div>
					<div class="form-group">
						<label>Rating:</label>
						<input type="text" name="rating" class="form-control">
					</div>
					<div class="form-group">
						<label>Awards:</label>
						<input type="text" name="awards" class="form-control">
					</div>
					<div class="form-group">
						<label>Release date:</label>
						<input type="date" name="release_date" class="form-control">
					</div>
					<button type="submit" class="btn btn-success">GUARDAR</button>
				</form>
			</div>
		</div>
	</div>

	<script>
		// Lista HTML donde se cargarán las películas que vienen de la DB
		let ul = document.querySelector('ul');

		// Tag donde mostraremos cuantas películas hay en la DB
		let span = document.querySelector('h2 span');

		// Formulario con el que estamos guardando una película
		let form = document.querySelector('form');

		// Array de los campos del Formulario, sacamos el último pues es el botón de enviar
		let campos = Array.from(form.elements);
		campos.pop();

		// Cabecera CSRF para que Laravel recibe el $request y guarde la película
		let header = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

		// metodo para cargar las peliculas

		function actualizarPeliculas() {
			ul.innerHTML = "";
			window.fetch('/api/getMovies')
				.then(response => response.json())
				.then(data => {
					span.innerHTML = data.length;
					data.forEach(pelicula => {
						ul.innerHTML += "<li id="+pelicula.id+">" + pelicula.title + "<div><button class='btnEditar' id="+pelicula.id+" name="+pelicula.title+">Editar</button><button class='btnBorrar' id="+pelicula.id+" name="+pelicula.title+">X</button></div></li>";
					});
					let btnsBorrar = document.querySelectorAll('.btnBorrar');
					btnsBorrar.forEach(btn=>{
						// console.log(li);
						btn.onclick= function(){
							let confirma = confirm('Desea eliminar la pelicula '+ btn.name);
							if(confirma){
								alert("Se va a eliminar "+  btn.name);
							deleteMovie(this.getAttribute('id'));
							}
						}
					});

					let btnsEditar = document.querySelectorAll('.btnEditar');
					btnsEditar.forEach(btn=>{
						// console.log(li);
						btn.onclick= function(){
							let confirma = confirm('Desea editar la pelicula '+ btn.name);
							if(confirma){
							let pelicula = data.find(pelicula=>pelicula.id == this.getAttribute('id'));
							alert(pelicula);
							editMovie(pelicula);
							}
						}
					});

				})
				.catch(error => {
					console.log(error)
				});
		}

		function deleteMovie(id) {
			fetch("/api/delete/" + id)
				.then(response => response.json())
				.then(data => {
					alert("Película eliminada exitosamente");
					actualizarPeliculas();
				})
				.catch(error => alert("No se pudo elimimar correctamente la película"));
		}

		function editMovie(movie) {
			campos[1].value = movie.id;
			campos[2].value = movie.title;
			campos[3].value = movie.rating;
			campos[4].value = movie.awards;
			campos[5].value = movie.release_date.slice(0, 10);
		}

		actualizarPeliculas();

		campos.forEach(campo => {
			campo.onblur = function() {
				// this.value == "" ? this.style.backgroundColor="red" : this.style.backgroundColor="white";
				this.value == "" ? this.classList.add("error") : this.classList.remove("error");

				if (this.name == "title" && this.value.length < 3) {
					let message = "El campo " + this.name + " debe tener más de 3 caracteres";
					alert(message);
				}
			}
		});

		form.onsubmit = e => {
			e.preventDefault();
			let errores = 0;

			campos.forEach(campo => {
				if (campo == "") {
					return errores++
				}
			});

			let DATA = new FormData(form);
			let token = document.querySelector("input[name='_token']");

			DATA.append('_token', token.value);

			let obj = {};
			DATA.forEach((value, key) => {
				obj[key] = value
			});


			if (!errores) {
				fetch('/api/guardarPelicula', {
						method: 'POST',
						body: JSON.stringify(obj),
						headers: {
							'Accept': 'application/json',
							'Content-Type': 'application/json',
							'Access-Control-Allow-Origin': '*'
						}
					}).then(response => response)
					.then(data => {
						// console.log(data);
						alert("Pelicula guardada exitosamente!");
						actualizarPeliculas();
					})
					.catch(error => {
						let mensaje = "No se pudo guardar la peliculas, intente en otro momento";
						//console.log(mensaje + ": Error - " + error);
						alert(mensaje);
					});
			}
		}
	</script>
</body>

</html>