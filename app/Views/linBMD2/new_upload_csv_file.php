    <div class="main-container">
        <!-- Navigation Bar with Tools Dropdown -->
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="navbar-nav">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="toolsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Tools
                    </a>
                    <div class="dropdown-menu" aria-labelledby="toolsDropdown">
                        <a class="dropdown-item" href="#" onclick="document.getElementById('csvFileInput').click(); return false;">Upload CSV</a>
                        <a class="dropdown-item" href="#" onclick="showDemoToast()">Demo Toast</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="javascript:location.reload();">Refresh Page</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hidden file input for CSV upload -->
        <input type="file" id="csvFileInput" accept=".csv" style="display: none;" onchange="handleFileUpload(this)">
    </div>

    <!-- Toast Container -->
    <div class="toast-container">
        <div id="errorToast" class="toast toast-error" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="false">
            <div class="toast-header">
                <strong class="mr-auto text-white">Error</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <div class="toast-body">
                <span id="errorMessage"></span>
            </div>
        </div>
        <div id="successToast" class="toast toast-success" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="false">
            <div class="toast-header">
                <strong class="mr-auto text-white">Success</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <div class="toast-body">
                <span id="successMessage"></span>
            </div>
        </div>

    </div>


    <script>
        let selectedFile = null;

        function handleFileUpload(input) {
            const file = input.files[0];
            
            if (!file) {
                return;
            }

            // File is selected
            selectedFile = file;
            
            // Automatically simulate upload
            setTimeout(() => {
                showSuccessToast('File "' + file.name + '" uploaded successfully!');
            }, 2000);
        }

        function showErrorToast(message) {
            document.getElementById('errorMessage').textContent = message;
            var errorToast = new bootstrap.Toast(document.getElementById('errorToast'));
            errorToast.show();
        }

        function showSuccessToast(message) {
            document.getElementById('successMessage').textContent = message;
            var successToast = new bootstrap.Toast(document.getElementById('successToast'));
            successToast.show();
        }

        // Demo function to test toast manually
        function showDemoToast() {
            showSuccessToast('This is a demo toast');
        }

        // Initialize tooltips and other Bootstrap components
        document.addEventListener('DOMContentLoaded', function() {
            console.log('CSV Upload Demo loaded successfully!');
        });
    </script>
