        <script>
            document.addEventListener('submit', async function (event) {
                var form = event.target;
                if (!form.classList.contains('js-delete-paid-registration')) return;
                event.preventDefault();
                var first = await Swal.fire({
                    title: 'Excluir inscrição paga?',
                    text: 'Deseja excluir a inscrição de ' + form.dataset.player + '?',
                    icon: 'warning', showCancelButton: true, focusCancel: true,
                    confirmButtonText: 'Continuar', cancelButtonText: 'Cancelar'
                });
                if (!first.isConfirmed) return;
                var second = await Swal.fire({
                    title: 'Confirmar exclusão',
                    text: 'A inscrição deixará de participar do evento e de seus processos. Esta ação não realiza estorno do pagamento. Confirma a exclusão?',
                    icon: 'warning', showCancelButton: true, focusCancel: true,
                    confirmButtonColor: '#d33', confirmButtonText: 'Sim, excluir inscrição', cancelButtonText: 'Cancelar'
                });
                if (second.isConfirmed) {
                    form.querySelector('button').disabled = true;
                    HTMLFormElement.prototype.submit.call(form);
                }
            });
        </script>
