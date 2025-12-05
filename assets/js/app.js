

async function cargarAlbums(page = 1) {
    currentPage = page;
    const q = document.getElementById('q')?.value.trim() || '';
    const categoria = document.getElementById('categoriaFiltro')?.value || '';

    const params = new URLSearchParams({ q, categoria, page });
    const res = await fetch(`api.php?${params}`);
    const json = await res.json();

    const contenedor = document.getElementById('contenedor-albums');
    contenedor.innerHTML = '';

    if (json.data.length === 0) {
        contenedor.innerHTML = '<p class="no-results">No se encontraron resultados.</p>';
        return;
    }

    json.data.forEach(item => {
        contenedor.innerHTML += `
            <article class="card">
                <img src="${item.url || 'https://picsum.photos/seed/default/400/250'}" alt="Portada de ${item.titulo}">
                <div class="card-body">
                    <div class="meta">
                        <span class="badge"><a href="?categoria=${encodeURIComponent(item.categoria)}">${item.categoria}</a></span>
                        <time class="anio">${item.anio || ''}</time>
                    </div>
                    <h3>${item.titulo}</h3>
                    <p class="desc">${item.descripcion}</p>
                </div>
                <div class="card-actions">
                    <button class="btn-edit" data-id="${item.Id}">Editar</button>
                    <button class="btn-delete" data-id="${item.Id}">Eliminar</button>
                </div>
            </article>
        `;
    });

    if (json.pagination.total > json.pagination.limit) {
        let pag = `<div class="pagination" style="grid-column:1/-1;text-align:center;margin:40px 0;font-size:1.2rem;">`;
        if (json.pagination.current > 1) {
            pag += `<button onclick="cargarAlbums(${json.pagination.current - 1})" class="btn-pagination">Anterior</button>`;
        }
        pag += `<span style="margin:0 25px;font-weight:bold;">Página ${json.pagination.current} de ${json.pagination.pages}</span>`;
        if (json.pagination.current < json.pagination.pages) {
            pag += `<button onclick="cargarAlbums(${json.pagination.current + 1})" class="btn-pagination">Siguiente</button>`;
        }
        pag += `</div>`;
        contenedor.innerHTML += pag;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const formulario = document.getElementById('form-busqueda');
    const contenedor = document.getElementById('contenedor-albums');

    formulario.addEventListener('submit', e => {
        e.preventDefault();
        cargarAlbums(1);
    });

    const formSugerir = document.getElementById('form-sugerir');
    if (formSugerir) {
        formSugerir.addEventListener('submit', async e => {
            e.preventDefault();
            const fd = new FormData(formSugerir);
            const res = await fetch('agregar.php', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.status === 'success') {
                Swal.fire({ icon: 'success', title: '¡Listo!', text: 'Álbum agregado', timer: 1500, showConfirmButton: false });
                formSugerir.reset();
                cargarAlbums(1);
            } else {
                Swal.fire('Error', data.message || 'No se pudo agregar', 'error');
            }
        });
    }

    const modal = document.createElement('div');
    modal.id = 'edit-modal';
    modal.style.cssText = 'display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:var(--card);padding:30px;border-radius:12px;max-width:540px;width:90%;box-shadow:0 15px 50px rgba(0,0,0,0.4);z-index:1000;border:1px solid var(--card-border);';
    modal.innerHTML = `
        <h2 style="margin:0 0 20px">Editar Álbum</h2>
        <form id="form-edit">
            <input type="hidden" name="id">
            <div style="margin-bottom:15px;"><label>Título *</label><input type="text" name="title" required style="width:100%;padding:10px;margin-top:5px;border-radius:6px;border:1px solid var(--card-border);"></div>
            <div style="margin-bottom:15px;"><label>Artista</label><input type="text" name="artist" style="width:100%;padding:10px;margin-top:5px;border-radius:6px;border:1px solid var(--card-border);"></div>
            <div style="margin-bottom:15px;"><label>Categoría *</label><input type="text" name="categoria" required style="width:100%;padding:10px;margin-top:5px;border-radius:6px;border:1px solid var(--card-border);"></div>
            <div style="margin-bottom:15px;"><label>Año</label><input type="number" name="anio" style="width:100%;padding:10px;margin-top:5px;border-radius:6px;border:1px solid var(--card-border);"></div>
            <div style="margin-bottom:15px;"><label>Descripción *</label><textarea name="descripcion" required rows="3" style="width:100%;padding:10px;margin-top:5px;border-radius:6px;border:1px solid var(--card-border);"></textarea></div>
            <div style="margin-bottom:20px;"><label>URL de imagen</label><input type="text" name="imagen" style="width:100%;padding:10px;margin-top:5px;border-radius:6px;border:1px solid var(--card-border);"></div>
            <div style="display:flex;gap:12px;justify-content:flex-end;">
                <button type="submit" style="padding:10px 20px;background:var(--accent);color:white;border:none;border-radius:6px;cursor:pointer;">Guardar</button>
                <button type="button" id="close-modal" style="padding:10px 20px;background:#6b7280;color:white;border:none;border-radius:6px;cursor:pointer;">Cancelar</button>
            </div>
        </form>
    `;
    document.body.appendChild(modal);
    document.getElementById('close-modal').onclick = () => modal.style.display = 'none';

    contenedor.addEventListener('click', async e => {
        if (e.target.classList.contains('btn-edit')) {
            const id = e.target.dataset.id;
            const res = await fetch(`api.php?id=${id}`);
            const album = (await res.json())[0];
            if (!album) return Swal.fire('Error', 'Álbum no encontrado', 'error');
            modal.querySelector('[name="id"]').value = album.Id;
            modal.querySelector('[name="title"]').value = album.titulo || '';
            modal.querySelector('[name="artist"]').value = album.artista || '';
            modal.querySelector('[name="categoria"]').value = album.categoria || '';
            modal.querySelector('[name="anio"]').value = album.anio || '';
            modal.querySelector('[name="descripcion"]').value = album.descripcion || '';
            modal.querySelector('[name="imagen"]').value = album.url || '';
            modal.style.display = 'block';
        }

        if (e.target.classList.contains('btn-delete')) {
            const id = e.target.dataset.id;
            const confirm = await Swal.fire({
                title: '¿Eliminar álbum?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });
            if (confirm.isConfirmed) {
                const fd = new FormData();
                fd.append('action', 'delete');
                fd.append('id', id);
                const res = await fetch('acciones.php', { method: 'POST', body: fd });
                const data = await res.json();
                if (data.status === 'success') {
                    Swal.fire('¡Eliminado!', '', 'success');
                    cargarAlbums(currentPage);
                } else {
                    Swal.fire('Error', data.message || 'No se pudo eliminar', 'error');
                }
            }
        }
    });

    document.getElementById('form-edit').addEventListener('submit', async e => {
        e.preventDefault();
        const fd = new FormData(e.target);
        fd.append('action', 'edit');
        const res = await fetch('acciones.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.status === 'success') {
            Swal.fire('¡Guardado!', '', 'success');
            modal.style.display = 'none';
            cargarAlbums(currentPage);
        } else {
            Swal.fire('Error', data.message || 'No se pudo guardar', 'error');
        }
    });

    cargarAlbums(1);
});