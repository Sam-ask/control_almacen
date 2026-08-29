let listaRecepcionTemporal = []; 

document.addEventListener('DOMContentLoaded', () => {
    cargarOrdenes();
    cargarCatalogosOrdenes();

    const formSubirPO = document.getElementById('formSubirPO');
    const alertMessage = document.getElementById('alertMessage');
    const btnSubmitPO = document.getElementById('btnSubmitPO');
    const formAclaracion = document.getElementById('formAclaracion');
    const btnEnviarAclaracion = document.getElementById('btnEnviarAclaracion');
    const formFiltros = document.getElementById('formFiltros');
    const formAgregarProducto = document.getElementById('formAgregarProducto');
    const btnGuardarRecepcion = document.getElementById('btnGuardarRecepcion');
    const formEditarPO = document.getElementById('formEditarPO'); // NUEVO

    if (formFiltros) {
        formFiltros.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(formFiltros);
            const queryParams = new URLSearchParams(formData).toString();
            cargarOrdenes('&' + queryParams);
        });
    }

    if (formSubirPO) {
        formSubirPO.addEventListener('submit', async (e) => {
            e.preventDefault();
            alertMessage.classList.add('d-none');
            btnSubmitPO.disabled = true;
            btnSubmitPO.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';

            const formData = new FormData(formSubirPO);

            try {
                const response = await fetch('../controllers/OrderController.php?action=subir', {
                    method: 'POST',
                    body: formData 
                });
                const result = await response.json();

                if (result.status === 'success') {
                    mostrarAlerta(result.message, 'success');
                    formSubirPO.reset();
                    cargarOrdenes(); 
                } else {
                    mostrarAlerta(result.message, 'danger');
                }
            } catch (error) {
                console.error('Error de red:', error);
                mostrarAlerta('Ocurrió un error al intentar comunicar con el servidor.', 'danger');
            } finally {
                btnSubmitPO.disabled = false;
                btnSubmitPO.innerHTML = '<i class="bi bi-cloud-arrow-up"></i> Guardar y Subir Archivo';
            }
        });
    }

    if (formAclaracion) {
        formAclaracion.addEventListener('submit', async (e) => {
            e.preventDefault();
            btnEnviarAclaracion.disabled = true;
            btnEnviarAclaracion.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Enviando...';

            const formData = new FormData(formAclaracion);

            try {
                const response = await fetch('../controllers/MailController.php?action=aclarar', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status === 'success' || result.status === 'warning') {
                    alert(result.message);
                    bootstrap.Modal.getInstance(document.getElementById('modalAclaracion')).hide();
                    formAclaracion.reset();
                    cargarOrdenes();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al intentar enviar el correo.');
            } finally {
                btnEnviarAclaracion.disabled = false;
                btnEnviarAclaracion.innerHTML = 'Enviar Correo';
            }
        });
    }

    if (formAgregarProducto) {
        formAgregarProducto.addEventListener('submit', (e) => {
            e.preventDefault();
            const select = document.getElementById('selectProductoRecepcion');
            const inputCantidad = document.getElementById('inputCantidadRecepcion');
            
            const idProducto = select.value;
            const nombreProducto = select.options[select.selectedIndex].dataset.nombre;
            const cantidad = parseFloat(inputCantidad.value);

            const indexExistente = listaRecepcionTemporal.findIndex(item => item.id_producto === idProducto);
            
            if(indexExistente !== -1) {
                listaRecepcionTemporal[indexExistente].cantidad += cantidad;
            } else {
                listaRecepcionTemporal.push({ 
                    id_producto: idProducto, 
                    nombre: nombreProducto, 
                    cantidad: cantidad 
                });
            }

            select.value = '';
            inputCantidad.value = '';
            renderizarTablaRecepcion();
        });
    }

    if (btnGuardarRecepcion) {
        btnGuardarRecepcion.addEventListener('click', async () => {
            if (listaRecepcionTemporal.length === 0) return;

            const id_orden = document.getElementById('recepcion_id_orden').value;
            
            btnGuardarRecepcion.disabled = true;
            btnGuardarRecepcion.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando Transacción...';

            const formData = new FormData();
            formData.append('id_orden', id_orden);
            formData.append('productos', JSON.stringify(listaRecepcionTemporal));

            try {
                const response = await fetch('../controllers/OrderController.php?action=procesar', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status === 'success') {
                    alert(result.message);
                    bootstrap.Modal.getInstance(document.getElementById('modalRecepcion')).hide();
                    cargarOrdenes(); 
                } else {
                    alert('Error: ' + result.message);
                    btnGuardarRecepcion.disabled = false;
                    btnGuardarRecepcion.innerHTML = 'Confirmar Recepción';
                }
            } catch (error) {
                console.error('Error Crítico:', error);
                alert('Ocurrió un error al intentar comunicar con el servidor.');
                btnGuardarRecepcion.disabled = false;
                btnGuardarRecepcion.innerHTML = 'Confirmar Recepción';
            }
        });
    }

    if (formEditarPO) {
        formEditarPO.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btnGuardar = document.getElementById('btnGuardarEdicion');
            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';

            const formData = new FormData(formEditarPO);
            try {
                const response = await fetch('../controllers/OrderController.php?action=editar', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status === 'success') {
                    alert(result.message);
                    bootstrap.Modal.getInstance(document.getElementById('modalEditarPO')).hide();
                    cargarOrdenes();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Ocurrió un error al guardar.');
            } finally {
                btnGuardar.disabled = false;
                btnGuardar.innerHTML = 'Guardar Cambios';
            }
        });
    }

    function mostrarAlerta(mensaje, tipo) {
        alertMessage.textContent = mensaje;
        alertMessage.className = `alert alert-${tipo} mt-3 mb-0`;
    }
});

async function cargarOrdenes(parametrosUrl = '') {
    const tbody = document.getElementById('tablaOrdenes');
    tbody.innerHTML = '<tr><td colspan="5" class="text-center"><span class="spinner-border spinner-border-sm"></span> Cargando...</td></tr>';

    try {
        const response = await fetch(`../controllers/OrderController.php?action=listar${parametrosUrl}`);
        const result = await response.json();
        
        if (result.status === 'success') {
            tbody.innerHTML = ''; 

            if (result.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No hay órdenes registradas.</td></tr>';
                return;
            }

            result.data.forEach(orden => {
                let badgeClass = 'bg-secondary';
                if (orden.estado === 'En Recepción') badgeClass = 'bg-primary';
                if (orden.estado === 'En Aclaración') badgeClass = 'bg-warning text-dark';
                if (orden.estado === 'Procesada') badgeClass = 'bg-success';

                let badgeUrgencia = orden.urgencia == 1 ? '<span class="badge bg-danger ms-1"><i class="bi bi-fire"></i> URGENTE</span>' : '';

                let botonEditar = '';
                let botonAclarar = '';
                let botonProcesar = '';
                let botonUrgencia = '';
                
                if (orden.estado !== 'Procesada') {
                    botonEditar = `<button class="btn btn-sm btn-outline-primary" onclick="abrirModalEditar(${orden.id})"><i class="bi bi-pencil"></i> Editar</button>`;
                    botonAclarar = `<button class="btn btn-sm btn-outline-warning" onclick="abrirModalAclaracion(${orden.id})"><i class="bi bi-envelope-exclamation"></i> Aclarar</button>`;
                    botonProcesar = `<button class="btn btn-sm btn-outline-success" onclick="abrirModalRecepcion(${orden.id})"><i class="bi bi-box-seam"></i> Procesar</button>`;

                    let colorBtn = orden.urgencia == 1 ? 'btn-outline-secondary' : 'btn-outline-danger';
                    let iconBtn = orden.urgencia == 1 ? 'bi-bell-slash' : 'bi-bell-fill';
                    let textBtn = orden.urgencia == 1 ? 'Quitar Urgencia' : 'Marcar Urgente';

                    botonUrgencia = `<button class="btn btn-sm ${colorBtn}" onclick="toggleUrgencia(${orden.id}, ${orden.urgencia})"><i class="bi ${iconBtn}"></i> ${textBtn}</button>`;
                }

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="align-middle">
                        <strong>${orden.numero_orden}</strong><br>
                        <small class="text-muted">${orden.fecha_creacion.substring(0, 10)}</small>
                    </td>
                    <td class="align-middle">
                        ${orden.proveedor}<br>
                        <span class="badge bg-light text-dark border"><i class="bi bi-building"></i> ${orden.planta}</span>
                    </td>
                    <td class="align-middle">
                        <span class="badge ${badgeClass}">${orden.estado}</span>
                        ${badgeUrgencia}
                    </td>
                    <td class="align-middle">
                        <a href="${orden.ruta_archivo}" target="_blank" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-file-pdf"></i> Ver PO
                        </a>
                    </td>
                    <td class="align-middle text-center">
                        <div class="d-flex gap-1 flex-wrap justify-content-center">
                            ${botonEditar}
                            ${botonAclarar}
                            ${botonProcesar}
                            ${botonUrgencia}
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
    } catch (error) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error al cargar las órdenes.</td></tr>';
        console.error('Error de red:', error);
    }
}

async function cargarCatalogosOrdenes() {
    const selectProv = document.getElementById('selectProveedor');
    const selectEmb = document.getElementById('selectEmbarcacion');
    const filtroProv = document.getElementById('filtroProveedor'); 

    try {
        const response = await fetch('../controllers/CatalogController.php?action=getOrdenesSelects');
        const result = await response.json();

        if (result.status === 'success') {
            if (selectProv) {
                let opcionesProv = '<option value="">Seleccione un proveedor...</option>';
                result.data.proveedores.forEach(p => {
                    opcionesProv += `<option value="${p.id}">${p.empresa}</option>`;
                });
                selectProv.innerHTML = opcionesProv;
            }
            
            if (filtroProv) {
                filtroProv.innerHTML = '<option value="">Todos los proveedores</option>' + result.data.proveedores.map(p => `<option value="${p.id}">${p.empresa}</option>`).join('');
            }

            if (selectEmb) {
                let opcionesEmb = '<option value="">Seleccione transporte...</option>';
                result.data.embarcaciones.forEach(e => {
                    opcionesEmb += `<option value="${e.id}">${e.nombre} (${e.tipo})</option>`;
                });
                selectEmb.innerHTML = opcionesEmb;
            }
        }
    } catch (error) {
        console.error('Error al cargar catálogos:', error);
    }
}

function abrirModalAclaracion(id_orden) {
    document.getElementById('aclaracion_id_orden').value = id_orden;
    const modal = new bootstrap.Modal(document.getElementById('modalAclaracion'));
    modal.show();
}

async function abrirModalRecepcion(id_orden) {
    document.getElementById('recepcion_id_orden').value = id_orden;
    listaRecepcionTemporal = []; 
    renderizarTablaRecepcion();
    
    const select = document.getElementById('selectProductoRecepcion');
    try {
        const response = await fetch('../controllers/InventoryController.php?action=listar');
        const result = await response.json();
        
        if(result.status === 'success') {
            let options = '<option value="">Seleccione un producto...</option>';
            result.data.forEach(prod => {
                options += `<option value="${prod.id}" data-nombre="${prod.nombre} (${prod.sku})">
                                [${prod.sku}] - ${prod.nombre}
                            </option>`;
            });
            select.innerHTML = options;
        }
    } catch (e) {
        select.innerHTML = '<option value="">Error al cargar catálogo</option>';
    }

    const modal = new bootstrap.Modal(document.getElementById('modalRecepcion'));
    modal.show();
}

function renderizarTablaRecepcion() {
    const tbody = document.getElementById('tablaProductosRecepcion');
    const btnGuardar = document.getElementById('btnGuardarRecepcion');
    
    if (listaRecepcionTemporal.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Aún no hay mercancía en la lista.</td></tr>';
        if(btnGuardar) btnGuardar.disabled = true;
        return;
    }

    tbody.innerHTML = '';
    listaRecepcionTemporal.forEach((item, index) => {
        tbody.innerHTML += `
            <tr>
                <td>${item.nombre}</td>
                <td><strong>${item.cantidad}</strong></td>
                <td>
                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarItemRecepcion(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    if(btnGuardar) btnGuardar.disabled = false;
}

function eliminarItemRecepcion(index) {
    listaRecepcionTemporal.splice(index, 1);
    renderizarTablaRecepcion();
}

async function toggleUrgencia(id_orden, urgencia_actual) {
    const formData = new FormData();
    formData.append('id_orden', id_orden);
    formData.append('urgencia_actual', urgencia_actual);

    try {
        const response = await fetch('../controllers/OrderController.php?action=toggle_urgencia', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status === 'success') {
            cargarOrdenes(); 
        } else {
            alert('Error al cambiar urgencia: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function abrirModalEditar(id_orden) {
    try {
        document.getElementById('editar_selectProveedor').innerHTML = document.getElementById('selectProveedor').innerHTML;
        document.getElementById('editar_selectEmbarcacion').innerHTML = document.getElementById('selectEmbarcacion').innerHTML;

        const response = await fetch(`../controllers/OrderController.php?action=obtener&id=${id_orden}`);
        const result = await response.json();
        
        if (result.status === 'success') {
            document.getElementById('editar_id_orden').value = result.data.id;
            document.getElementById('editar_numero_orden').value = result.data.numero_orden;
            document.getElementById('editar_selectProveedor').value = result.data.id_proveedor;
            document.getElementById('editar_selectEmbarcacion').value = result.data.id_embarcacion;
            
            const modal = new bootstrap.Modal(document.getElementById('modalEditarPO'));
            modal.show();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al obtener los datos de la orden.');
    }
}