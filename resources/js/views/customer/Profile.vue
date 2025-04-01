<template>
    <div class="page-content pt-150 pb-150">
      <div class="container">
        <div class="row">
          <!-- Sidebar Menu -->
          <div class="col-lg-10 m-auto">
            <div class="row">
              <div class="col-md-3">
                <div class="dashboard-menu">
                  <ul class="nav flex-column" role="tablist">
                    <li class="nav-item">
                      <a
                        class="nav-link active"
                        id="orders-tab"
                        data-bs-toggle="tab"
                        href="#orders"
                        role="tab"
                        aria-controls="orders"
                        aria-selected="true"
                      >
                        <i class="fi-rs-shopping-bag mr-10"></i>Orders
                      </a>
                    </li>
                    <li class="nav-item">
                      <a
                        class="nav-link"
                        id="account-detail-tab"
                        data-bs-toggle="tab"
                        href="#account-detail"
                        role="tab"
                        aria-controls="account-detail"
                        aria-selected="false"
                      >
                        <i class="fi-rs-user mr-10"></i>Account details
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#" @click.prevent="logout">
                        <i class="fi-rs-sign-out mr-10"></i>Logout
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
              <!-- Main Content -->
              <div class="col-md-9">
                <div class="tab-content account dashboard-content pl-50">
                  <!-- Orders Tab -->
                  <div
                    class="tab-pane fade show active"
                    id="orders"
                    role="tabpanel"
                    aria-labelledby="orders-tab"
                  >
                    <div class="card">
                      <div class="card-header">
                        <h3 class="mb-0">Your Orders</h3>
                      </div>
                      <div class="card-body">
                        <div v-if="orders.length" class="table-responsive">
                          <table class="table">
                            <thead>
                              <tr>
                                <th>Order</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Actions</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="order in orders" :key="order.id">
                                <td>#{{ order.id }}</td>
                                <td>{{ formatDate(order.created_at) }}</td>
                                <td>{{ order.status }}</td>
                                <td>
                                  ${{ parseFloat(order.total).toFixed(2) }} for
                                  {{ order.order_items.length }} item(s)
                                </td>
                                <td>
                                  <button class="btn btn-sm" @click="viewOrder(order)">
                                    View
                                  </button>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <div v-else>
                          <p>You have no orders.</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Account Details Tab -->
                  <div
                    class="tab-pane fade"
                    id="account-detail"
                    role="tabpanel"
                    aria-labelledby="account-detail-tab"
                  >
                    <div class="card">
                      <div class="card-header">
                        <h5>Account Details</h5>
                      </div>
                      <div class="card-body">
                        <p>
                          Forgot your password? <a href="#">Reset it here!</a>
                        </p>
                        <form @submit.prevent="updateProfile">
                          <div class="row">
                            <div class="form-group col-md-12">
                              <label>Name <span class="required">*</span></label>
                              <input
                                v-model="profile.name"
                                required
                                class="form-control"
                                type="text"
                              />
                            </div>
                            <div class="form-group col-md-12">
                              <label>Email Address <span class="required">*</span></label>
                              <input
                                v-model="profile.email"
                                required
                                class="form-control"
                                type="email"
                              />
                            </div>
                            <div class="form-group col-md-6">
                              <button
                                type="submit"
                                class="form-control button-primary"
                                style="background-color: #3bb77e; color: #fff;"
                              >
                                Save Changes
                              </button>
                            </div>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- End Main Content -->
            </div>
          </div>
        </div>
      </div>
  
      <!-- Order Details Modal -->
      <div
        class="modal fade"
        id="orderDetailsModal"
        tabindex="-1"
        aria-labelledby="orderDetailsModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="orderDetailsModalLabel">
                Order Details - #{{ selectedOrder.id }}
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div v-if="selectedOrder.order_items && selectedOrder.order_items.length">
                <ul>
                  <li v-for="item in selectedOrder.order_items" :key="item.id">
                    {{ item.product.title }} - Qty: {{ item.quantity }} - Subtotal:
                    ${{ parseFloat(item.total).toFixed(2) }}
                  </li>
                </ul>
                <p>
                  <strong>Total:</strong> ${{ parseFloat(selectedOrder.total).toFixed(2) }}
                </p>
              </div>
              <div v-else>
                <p>No order items found.</p>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { useRouter } from 'vue-router';
  import apiClient from '@/api/index.js';
  
  const orders = ref([]);
  const profile = ref({
    name: '',
    email: ''
  });
  const selectedOrder = ref({});
  
  const router = useRouter();
  
  // Helper: Format date string
  const formatDate = (dateStr) => {
    const date = new Date(dateStr);
    return date.toLocaleDateString();
  };
  
  const fetchOrders = async () => {
    try {
      const response = await apiClient.get('/customer/orders');
      if (response.data.success) {
        orders.value = response.data.data;
      }
    } catch (error) {
      console.error('Error fetching orders:', error);
    }
  };
  
  const fetchProfile = async () => {
    try {
      const response = await apiClient.get('/customer/profile');
      if (response.data.success) {
        profile.value = response.data.user;
      }
    } catch (error) {
      console.error('Error fetching profile:', error);
    }
  };
  
  const updateProfile = async () => {
    try {
      const response = await apiClient.put('/customer/profile/update', profile.value);
      if (response.data.success) {
        alert('Profile updated successfully.');
      } else {
        alert('Failed to update profile.');
      }
    } catch (error) {
      console.error('Error updating profile:', error);
      alert('Error updating profile.');
    }
  };
  
  const viewOrder = (order) => {
    selectedOrder.value = order;
    // Show modal (assumes Bootstrap 5 JS is loaded globally)
    const modalElement = document.getElementById('orderDetailsModal');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
  };
  
  const logout = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('role');
    router.push('/login');
  };
  
  onMounted(() => {
    fetchOrders();
    fetchProfile();
  });
  </script>
  
  <style scoped>
  .page-content {
    padding-top: 150px;
    padding-bottom: 150px;
  }
  .dashboard-menu ul {
    list-style: none;
    padding: 0;
  }
  .dashboard-menu ul li {
    margin-bottom: 10px;
  }
  .dashboard-menu ul li a {
    display: block;
    padding: 10px;
    color: #333;
    text-decoration: none;
  }
  .dashboard-menu ul li a.active,
  .dashboard-menu ul li a:hover {
    background-color: #f3f3f3;
    color: #3bb77e;
  }
  .card {
    margin-bottom: 20px;
  }
  .modal-header {
    background-color: #3bb77e;
    color: #fff;
  }
  </style>
  