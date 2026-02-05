@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Product Management</h2>
                </div>

                <!-- Form Card -->
                <div class="card shadow-sm mb-5">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Add New Product</h4>
                    </div>
                    <div class="card-body">
                        <form id="productForm">
                            <div class="row g-3">
                                <input type="hidden" name="id" id="id" value="">

                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="Product Name" required>
                                        <div class="text-danger small" data-error="name"></div>
                                        <label for="name">Product Name</label>
                                        <div class="form-text">Enter the product name</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="quantity" name="quantity"
                                            placeholder="Quantity" min="0" required>
                                        <div class="text-danger small" data-error="quantity"></div>
                                        <label for="quantity">Quantity in Stock</label>
                                        <div class="form-text">Enter available quantity</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="price" name="price"
                                            placeholder="Price" step="0.01" min="0" required>
                                        <div class="text-danger small" data-error="price"></div>
                                        <label for="price">Price per Item ($)</label>
                                        <div class="form-text">Enter unit price</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" id="addProduct" class="btn btn-primary btn-lg px-4">
                                        <i class="bi bi-plus-circle me-2"></i>Add Product
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="alertBox" class="mt-3"></div>

                <!-- Table Section -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h4 class="mb-0">Product Inventory</h4>
                    </div>
                    <div class="card-body p-0">
                        <div id="table-container" class="table-responsive">
                            <!-- Table will be inserted here by JavaScript -->
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-table display-4 d-block mb-3"></i>
                                <p class="mb-0">No products added yet. Start by adding your first product above.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const form = document.getElementById('productForm');
        const alertBox = document.getElementById('alertBox');
        const submitBtn = document.getElementById('addProduct');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            clearErrors();
            clearAlert();

            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Saving...';

            const formData = new FormData(form);
            const id = formData.get('id');

            let request;

            // UPDATE MODE
            if (id && id.trim() !== '') {
                request = axios.post(`/products/${id}?_method=PUT`, formData);
            }
            // CREATE MODE
            else {
                request = axios.post('/products', formData);
            }


            request.then(response => {
                    showSuccess(response.data.message);
                    renderTable(response.data.data);
                    form.reset();

                    // reset hidden id
                    document.getElementById('id').value = '';
                    submitBtn.innerHTML = '<i class="bi bi-plus-circle me-2"></i>Add Product';
                })
                .catch(error => {
                    if (error.response?.status === 422) {
                        showValidationErrors(error.response.data.errors);
                    } else {
                        showError(error.response?.data?.message || 'Something went wrong');
                    }
                })
                .finally(() => {
                    submitBtn.disabled = false;
                });
        });

        /* On click Edit Button */
        document.getElementById('table-container').addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('edit')) {
                const id = e.target.getAttribute('data-id');
                const row = e.target.closest('tr');
                const name = row.querySelector('td:nth-child(1)').textContent;
                const quantity = row.querySelector('td:nth-child(2)').textContent;
                const price = row.querySelector('td:nth-child(3)').textContent;

                form.querySelector('input[name="name"]').value = name;
                form.querySelector('input[name="quantity"]').value = quantity;
                form.querySelector('input[name="price"]').value = price;
                form.querySelector('input[name="id"]').value = id;
            }
        });

        /* DELETE handler */
        document.getElementById('table-container').addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('delete')) {
                const id = e.target.dataset.id;

                if (!confirm('Are you sure you want to delete this product?')) {
                    return;
                }

                axios.delete(`/products/${id}`)
                    .then(res => {
                        showSuccess(res.data.message);
                        renderTable(res.data.data);
                    })
                    .catch(err => {
                        showError(err.response?.data?.message || 'Delete failed');
                    });
            }

        });



        /* ---------------- Helper Functions ---------------- */

        function showValidationErrors(errors) {
            Object.keys(errors).forEach(field => {
                const errorEl = document.querySelector(`[data-error="${field}"]`);
                if (errorEl) {
                    errorEl.innerText = errors[field][0];
                }
            });
        }

        function clearErrors() {
            document.querySelectorAll('[data-error]').forEach(el => el.innerText = '');
        }

        function showSuccess(message) {
            alertBox.innerHTML = `
                <div class="alert alert-success fade show">
                    ${message}
                </div>
            `;
        }

        function showError(message) {
            alertBox.innerHTML = `
                <div class="alert alert-danger fade show">
                    ${message}
                </div>
            `;
        }

        function clearAlert() {
            alertBox.innerHTML = '';
        }

        function renderTable(data) {
            axios.post('/products/render', data)
                .then(res => {
                    document.getElementById('table-container').innerHTML = res.data;
                })
                .catch(() => {
                    showError('Failed to refresh table');
                });
        }

        function loadProducts() {
            axios.get('/products/list')
                .then(res => {
                    document.getElementById('table-container').innerHTML = res.data;
                })
                .catch(() => {
                    showError('Failed to load products');
                });
        }

        function updateProduct(id, formData) {
            axios.put(`/products/${id}`, formData)
                .then(res => {
                    showSuccess(res.data.message);
                    renderTable(res.data.data);
                })
                .catch(err => {
                    if (err.response.status === 422) {

                        showValidationErrors(err.response.data.errors);

                    } else {

                        showError('Update failed');

                    }
                });
        }


        document.addEventListener('DOMContentLoaded', function() {
            loadProducts();
        });
    </script>
@endsection
