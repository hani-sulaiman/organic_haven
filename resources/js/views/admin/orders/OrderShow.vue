<template>
    <div class="container mt-4">
      <h1>Order Details</h1>
      <div v-if="order">
        <div class="card mb-4">
          <div class="card-header">
            Order #{{ order.id }}
          </div>
          <div class="card-body">
            <h5 class="card-title">User: {{ order.user.name }}</h5>
            <p class="card-text"><strong>Status:</strong> 
              <select @change="updateStatus(order.id, $event.target.value)" class="form-control form-control-sm d-inline-block w-auto">
                <option :value="order.status" selected>{{ order.status }}</option>
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </p>
            <p class="card-text"><strong>Total:</strong> ${{ order.total.toFixed(2) }}</p>
            <p class="card-text"><strong>Subtotal:</strong> ${{ order.subtotal.toFixed(2) }}</p>
            <p class="card-text"><strong>Tax:</strong> ${{ order.tax.toFixed(2) }}</p>
            <p class="card-text"><strong>Created At:</strong> {{ order.created_at }}</p>
            <button @click="deleteOrder(order.id)" class="btn btn-danger btn-sm mr-2"><i class="fas fa-trash-alt"></i> Delete</button>
            <button @click="printBill" class="btn btn-success btn-sm"><i class="fas fa-print"></i> Print Bill</button>
          </div>
        </div>
        <h2>Order Items</h2>
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>Product</th>
              <th>Quantity</th>
              <th>Price</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in order.order_items" :key="item.id">
              <td>{{ item.product.title }}</td>
              <td>{{ item.quantity }}</td>
              <td>${{ item.price.toFixed(2) }}</td>
              <td>${{ item.total.toFixed(2) }}</td>
            </tr>
          </tbody>
        </table>
        <Bill v-if="showBill" :order="order" @close-bill="closeBill" />
      </div>
      <div v-else>
        <p>Loading...</p>
      </div>
    </div>
  </template>
  
  <script>
  import apiClient from '@/api/index';
  import Bill from './Bill.vue';
  
  export default {
    components: {
      Bill,
    },
    data() {
      return {
        order: null,
        showBill: false,
      };
    },
    created() {
      this.fetchOrder(this.$route.params.id);
    },
    methods: {
      async fetchOrder(id) {
        try {
          const response = await apiClient.get(`/admin/orders/${id}`);
          // Convert total, subtotal, and tax to numbers
          this.order = {
            ...response.data.data,
            total: parseFloat(response.data.data.total) || 0,
            subtotal: parseFloat(response.data.data.subtotal) || 0,
            tax: parseFloat(response.data.data.tax) || 0,
            order_items: response.data.data.order_items.map(item => ({
              ...item,
              price: parseFloat(item.price) || 0,
              total: parseFloat(item.total) || 0,
            })),
          };
        } catch (error) {
          console.error('Error fetching order:', error);
        }
      },
      async updateStatus(id, status) {
        try {
          await apiClient.put(`/admin/orders/${id}`, {
            status: status,
          });
          this.fetchOrder(id);
        } catch (error) {
          console.error('Error updating order status:', error.response?.data || error.message);
        }
      },
      async deleteOrder(id) {
        try {
          await apiClient.delete(`/admin/orders/${id}`);
          this.$router.push({ name: 'admin.orders.index' });
        } catch (error) {
          console.error('Error deleting order:', error);
        }
      },
      printBill() {
        this.showBill = true;
        setTimeout(() => {
          window.print();
          this.closeBill(); // Close the bill after printing
        }, 100); // Delay to ensure the bill is fully rendered
      },
      closeBill() {
        this.showBill = false;
      },
    },
  };
  </script>
  
  <style scoped>
  .container {
    max-width: 1200px;
  }
  
  .table th {
    background-color: #f8f9fa;
  }
  
  .table td {
    vertical-align: middle;
  }
  
  .btn-group {
    margin-top: auto;
  }
  </style>