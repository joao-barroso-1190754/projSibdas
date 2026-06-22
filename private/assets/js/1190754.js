const menuToggle = document.getElementById("menu-toggle");
const sidebarWrapper = document.getElementById("sidebar-wrapper");

menuToggle.addEventListener("click", (e) => {
    e.preventDefault();
    sidebarWrapper.classList.toggle("collapsed");
});

document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('.delete-form');
            const title = this.dataset.confirmTitle || 'Remover registo?';
            const text = this.dataset.confirmText || 'Esta ação não pode ser revertida.';
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, remover!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) { form.submit(); }
            });
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Report Issue Alert
    const reportButtons = document.querySelectorAll('.btn-report');
    reportButtons.forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('.report-form');
            Swal.fire({
                title: 'Reportar Avaria',
                html: "O equipamento será marcado com estado 'Em manutenção'.",
                input: 'textarea',
                inputLabel: 'Descrição da avaria (opcional)',
                inputPlaceholder: 'Ex: equipamento a fazer ruído estranho, ecrã não liga, etc.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, reportar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    let notaInput = form.querySelector('input[name="nota"]');
                    if (!notaInput) {
                        notaInput = document.createElement('input');
                        notaInput.type = 'hidden';
                        notaInput.name = 'nota';
                        form.appendChild(notaInput);
                    }
                    notaInput.value = result.value || '';
                    form.submit();
                }
            });
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Technician status update
    const updateButtons = document.querySelectorAll('.btn-update-status');
    updateButtons.forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('.status-update-form');
            const estadoAtual = this.dataset.estado;

            Swal.fire({
                title: 'Atualizar Estado do Equipamento',
                html:
                    '<select id="swal-estado" class="swal2-select">' +
                        '<option value="Ativo">Ativo</option>' +
                        '<option value="Em manutenção">Em manutenção</option>' +
                        '<option value="Em calibração">Em calibração</option>' +
                        '<option value="Inativo">Inativo</option>' +
                    '</select>' +
                    '<textarea id="swal-nota" class="swal2-textarea" placeholder="Nota sobre a intervenção (ex: peça substituída, calibração concluída...)"></textarea>',
                showCancelButton: true,
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar',
                didOpen: () => {
                    document.getElementById('swal-estado').value = estadoAtual;
                },
                preConfirm: () => {
                    return {
                        estado: document.getElementById('swal-estado').value,
                        nota: document.getElementById('swal-nota').value
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.querySelector('input[name="estado"]').value = result.value.estado;
                    form.querySelector('input[name="nota"]').value = result.value.nota;
                    form.submit();
                }
            });
        });
    });
});

function exportarPDF() {
    const element = document.getElementById('tabela-inventario');
    
    const opt = {
        margin:       10,
        filename:     'Inventario_Equipamentos.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
    };

    const acoesCells = element.querySelectorAll('th:last-child, td:last-child');
    acoesCells.forEach(cell => cell.style.display = 'none');

    html2pdf().set(opt).from(element).save().then(() => {
        acoesCells.forEach(cell => cell.style.display = '');
    });
}

function exportarHistoricoPDF() {
    const element = document.getElementById('tabela-historico');
 
    const opt = {
        margin:       10,
        filename:     'Historico_Intervencoes.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
    };
 
    html2pdf().set(opt).from(element).save();
}