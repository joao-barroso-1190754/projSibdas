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
            Swal.fire({
                title: 'Remover equipamento?',
                text: "O histórico e documentos associados também serão apagados!",
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
                title: 'Reportar Avaria?',
                text: "O equipamento será marcado com estado 'Em manutenção'.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107', // Warning yellow
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, reportar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) { form.submit(); }
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