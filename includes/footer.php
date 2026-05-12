    </div><!-- /#wrapper -->

    <!-- Bootstrap 5 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
    <script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
    
    <script>
        // Fungsi pembantu untuk memunculkan toast
        function triggerToast(message, type = 'primary') {
            const toastElement = document.getElementById("liveToast");
            const toastMessage = document.getElementById("toastMessage");
            
            if (toastElement && toastMessage) {
                toastMessage.innerText = message;
                
                // Atur warna berdasarkan type (success, danger, primary)
                toastElement.classList.remove('text-bg-primary', 'text-bg-success', 'text-bg-danger');
                const bgClass = type === 'success' ? 'text-bg-success' : (type === 'danger' ? 'text-bg-danger' : 'text-bg-primary');
                toastElement.classList.add(bgClass);
                
                const toast = new bootstrap.Toast(toastElement);
                toast.show();
            }
        }

        // 1. Deteksi status dari URL (untuk user yang melakukan aksi)
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const error = urlParams.get('error');

            if (status === 'success') {
                triggerToast('Action completed successfully!', 'success');
            } else if (error) {
                triggerToast('Error: ' + error, 'danger');
            }
        });

        // 2. Deteksi dari WebSocket (untuk realtime notification)
        const socket = io("http://localhost:3001");
        socket.on("notification", (data) => {
            console.log("Realtime Notification:", data);
            triggerToast(data.message, data.type || 'primary');
        });
    </script>
    </body>
    </html>