document.addEventListener('DOMContentLoaded', () => {
    const formulario = document.getElementById('form-busqueda');
    const contenedor = document.getElementById('contenedor-albums');

    if (formulario) {
        formulario.addEventListener('submit', async (e) => {
            e.preventDefault();
            const busqueda = encodeURIComponent(document.getElementById('q').value);
            const categoria = encodeURIComponent(document.getElementById('categoriaFiltro').value);

            try {
                const respuesta = await fetch(`api.php?q=${busqueda}&categoria=${categoria}`);
                const datos = await respuesta.json();
                mostrarAlbums(datos);
            } catch (error) {
                console.error('Error al cargar álbumes:', error);
            }
        });
    }

    function mostrarAlbums(albums) {
        contenedor.innerHTML = '';

        if (albums.length === 0) {
            contenedor.innerHTML = '<p class="no-results">No se encontraron resultados.</p>';
            return;
        }

        albums.forEach(item => {
            const html = `
                <article class="card">
                    <img src="${item.url}" alt="Portada de ${item.titulo}">
                    <div class="card-body">
                        <div class="meta">
                            <span class="badge">${item.categoria}</span>
                            <time class="anio">${item.anio || ''}</time>
                        </div>
                        <h3>${item.titulo}</h3>
                        <p class="desc">${item.descripcion}</p>
                    </div>
                </article>
            `;
            contenedor.innerHTML += html;
        });
    }

    const formSugerir = document.getElementById('form-sugerir');

    if (formSugerir) {
        formSugerir.addEventListener('submit', async (e) => {
            e.preventDefault();
            const datosFormulario = new FormData(formSugerir);

            try {
                const respuesta = await fetch('agregar.php', {
                    method: 'POST',
                    body: datosFormulario
                });
                const resultado = await respuesta.json();

                if (resultado.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Álbum Agregado!',
                        text: 'Tu sugerencia ha sido guardada con éxito.',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    formSugerir.reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: resultado.message
                    });
                }

            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor.'
                });
            }
        });
    }
});