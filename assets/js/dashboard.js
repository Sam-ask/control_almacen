document.addEventListener('DOMContentLoaded', () => {
    cargarUltimosMovimientos();
});

async function cargarUltimosMovimientos() {
    const tbody = document.getElementById('tablaMovimientos');
    
    try {
        const response = await fetch('../controllers/DashboardController.php?action=ultimos_movimientos');
        const result = await response.json();
        
        if (result.status === 'success') {
            tbody.innerHTML = ''; 
            
            if(result.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No hay movimientos recientes registrados en el Kardex.</td></tr>';
                return;
            }
            
            result.data.forEach(mov => {
                const fecha = mov.fecha_movimiento ? mov.fecha_movimiento.substring(0, 16) : 'N/A';
                const badgeClass = mov.tipo_movimiento === 'ENTRADA' ? 'bg-success' : 'bg-danger';
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="text-muted small align-middle">${fecha}</td>
                    <td class="align-middle"><span class="badge ${badgeClass}">${mov.tipo_movimiento}</span></td>
                    <td class="align-middle"><strong>${mov.sku}</strong> - <span class="text-muted">${mov.producto}</span></td>
                    <td class="align-middle"><strong>${mov.cantidad}</strong></td>
                    <td class="align-middle"><span class="badge bg-secondary border">${mov.numero_orden || 'S/N'}</span></td>
                `;
                tbody.appendChild(tr);
            });
        }
    } catch (error) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-3"><i class="bi bi-exclamation-triangle"></i> Error de conexión al cargar datos.</td></tr>';
        console.error('Error Fetch Dashboard:', error);
    }
}
document.addEventListener('DOMContentLoaded', () => {
    cargarResumenDashboard();
});

async function cargarResumenDashboard() {
    const tbody = document.getElementById('tablaMovimientos');
    
    try {
        const response = await fetch('../controllers/DashboardController.php?action=resumen');
        const result = await response.json();
        
        if (result.status === 'success') {
            document.getElementById('kpiPendientes').textContent = result.data.kpis.pendientes;
            document.getElementById('kpiAclaracion').textContent = result.data.kpis.aclaracion;
            document.getElementById('kpiStock').textContent = result.data.kpis.stock_critico;
            document.getElementById('kpiRecepciones').textContent = result.data.kpis.recepciones_hoy;
            tbody.innerHTML = ''; 
            
            if(result.data.movimientos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No hay movimientos recientes registrados en el Kardex.</td></tr>';
                return;
            }
            
            result.data.movimientos.forEach(mov => {
                const fecha = mov.fecha_movimiento ? mov.fecha_movimiento.substring(0, 16) : 'N/A';
                const badgeClass = mov.tipo_movimiento === 'ENTRADA' ? 'bg-success' : 'bg-danger';
                
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="text-muted small align-middle">${fecha}</td>
                    <td class="align-middle"><span class="badge ${badgeClass}">${mov.tipo_movimiento}</span></td>
                    <td class="align-middle"><strong>${mov.sku}</strong> - <span class="text-muted">${mov.producto}</span></td>
                    <td class="align-middle"><strong>${mov.cantidad}</strong></td>
                    <td class="align-middle"><span class="badge bg-secondary border">${mov.numero_orden || 'S/N'}</span></td>
                `;
                tbody.appendChild(tr);
            });
        }
    } catch (error) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-3"><i class="bi bi-exclamation-triangle"></i> Error de conexión al cargar datos.</td></tr>';
        console.error('Error Fetch Dashboard:', error);
    }
}