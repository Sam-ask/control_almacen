document.addEventListener('DOMContentLoaded', () => {
    cargarInventario();
    cargarCatalogos();
    
    const formFiltros = document.getElementById('formFiltrosInventario');
    if (formFiltros) {
        formFiltros.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(formFiltros);
            const queryParams = new URLSearchParams(formData).toString();
            cargarInventario('&' + queryParams);
        });
    }

    document.getElementById('formProducto').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);

        try {
            const response = await fetch('../controllers/InventoryController.php?action=guardar', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.status === 'success') {
                alert(result.message); 
                document.getElementById('formProducto').reset();
                bootstrap.Modal.getInstance(document.getElementById('modalNuevoProducto')).hide();
                cargarInventario();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error al guardar:', error);
        }
    });
});

async function cargarInventario(parametrosUrl = '') {
    const tbody = document.getElementById('tablaInventario');
    tbody.innerHTML = '<tr><td colspan="6" class="text-center"><span class="spinner-border spinner-border-sm text-primary"></span> Buscando...</td></tr>';
    
    try {
        const response = await fetch(`../controllers/InventoryController.php?action=listar${parametrosUrl}`);
        const result = await response.json();

        if (result.status === 'success') {
            tbody.innerHTML = ''; 
            
            if (result.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No se encontraron productos con esos filtros.</td></tr>';
                return;
            }

            result.data.forEach(prod => {
                const stockClase = parseFloat(prod.stock) <= parseFloat(prod.stock_minimo) 
                                   ? 'text-danger fw-bold' : 'text-success';

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>${prod.sku}</strong></td>
                    <td>${prod.nombre}</td>
                    <td><span class="badge bg-secondary">${prod.tipo_material}</span></td>
                    <td>${prod.proveedor}</td>
                    <td class="${stockClase}">${prod.stock}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarProducto(${prod.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
    } catch (error) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-danger text-center">Error al cargar datos.</td></tr>`;
        console.error('Error:', error);
    }
}

async function eliminarProducto(id) {
    if (confirm('¿Estás seguro de eliminar este producto del catálogo?')) {
        const formData = new FormData();
        formData.append('id', id);

        const response = await fetch('../controllers/InventoryController.php?action=eliminar', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status === 'success') {
            cargarInventario();
        } else {
            alert(result.message);
        }
    }
}

async function cargarCatalogos() {
    const selectMaterial = document.getElementById('selectMaterial');
    const selectProveedor = document.getElementById('selectProveedor');
    const filtroProveedor = document.getElementById('filtroProveedor'); 

    try {
        const response = await fetch('../controllers/CatalogController.php?action=getInventarioSelects');
        const result = await response.json();

        if (result.status === 'success') {
            // Llenar Materiales
            let opcionesMaterial = '<option value="">Seleccione un material...</option>';
            result.data.materiales.forEach(mat => {
                opcionesMaterial += `<option value="${mat.id}">${mat.nombre}</option>`;
            });
            if(selectMaterial) selectMaterial.innerHTML = opcionesMaterial;

            let opcionesProveedor = '<option value="">Seleccione un proveedor...</option>';
            let opcionesFiltro = '<option value="">Todos</option>';
            
            result.data.proveedores.forEach(prov => {
                opcionesProveedor += `<option value="${prov.id}">${prov.empresa}</option>`;
                opcionesFiltro += `<option value="${prov.id}">${prov.empresa}</option>`;
            });
            
            if(selectProveedor) selectProveedor.innerHTML = opcionesProveedor;
            if(filtroProveedor) filtroProveedor.innerHTML = opcionesFiltro;
        }
    } catch (error) {
        console.error('Error al cargar catálogos:', error);
    }
}
let badge = stock <= stock_minimo 
  ? `<span class="badge-stock-low"><i class="bi bi-exclamation-triangle-fill me-1"></i>${stock} Crítico</span>`
  : `<span class="badge-stock-ok"><i class="bi bi-check-circle-fill me-1"></i>${stock} En Stock</span>`;