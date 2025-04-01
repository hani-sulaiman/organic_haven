<template>
  <div class="container mt-4">
    <h1>Orders</h1>
    <div class="filters mb-4">
      <button @click="toggleForm" class="btn btn-primary mr-2">Create New Order</button>
    </div>
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th>User</th>
          <th>Status</th>
          <th>Total</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="order in orders.data" :key="order.id">
          <td>{{ order.id }}</td>
          <td>{{ order.user.name }}</td>
          <td>
            <select @change="updateStatus(order.id, $event.target.value)" class="form-control form-control-sm">
              <option :value="order.status" selected>{{ order.status }}</option>
              <option value="pending">Pending</option>
              <option value="paid">Paid</option>
              <option value="shipped">Shipped</option>
              <option value="delivered">Delivered</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </td>
          <td>${{ order.total.toFixed(2) }}</td>
          <td>{{ order.created_at }}</td>
          <td>
            <button @click="viewOrder(order.id)" class="btn btn-info btn-sm mr-2"><i class="fas fa-eye"></i> View</button>
            <button @click="deleteOrder(order.id)" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
    <nav aria-label="Page navigation" class="mt-4">
      <ul class="pagination justify-content-center">
        <li class="page-item" :class="{ disabled: orders.current_page === 1 }">
          <a class="page-link" href="#" @click.prevent="prevPage">Previous</a>
        </li>
        <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: orders.current_page === page }">
          <a class="page-link" href="#" @click.prevent="goToPage(page)">{{ page }}</a>
        </li>
        <li class="page-item" :class="{ disabled: orders.current_page === totalPages }">
          <a class="page-link" href="#" @click.prevent="nextPage">Next</a>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script>
import apiClient from '@/api/index';

export default {
  data() {
    return {
      orders: {
        data: [],
        current_page: 1,
        last_page: 1,
      },
      currentPage: 1,
      perPage: 20,
    };
  },
  computed: {
    totalPages() {
      return this.orders.last_page;
    },
  },
  created() {
    this.fetchOrders();
  },
  methods: {
    async fetchOrders() {
      try {
        const response = await apiClient.get('/admin/orders', {
          params: {
            page: this.currentPage,
            per_page: this.perPage,
          },
        });
        // Convert total, subtotal, and tax to numbers
        this.orders = {
          ...response.data.data,
          data: response.data.data.data.map(order => ({
            ...order,
            total: parseFloat(order.total) || 0,
            subtotal: parseFloat(order.subtotal) || 0,
            tax: parseFloat(order.tax) || 0,
          })),
        };
      } catch (error) {
        console.error('Error fetching orders:', error);
      }
    },
    async updateStatus(id, status) {
      try {
        await apiClient.put(`/admin/orders/${id}`, {
          status: status,
        });
        this.fetchOrders();
      } catch (error) {
        console.error('Error updating order status:', error.response?.data || error.message);
      }
    },
    async deleteOrder(id) {
      try {
        await apiClient.delete(`/admin/orders/${id}`);
        this.fetchOrders();
      } catch (error) {
        console.error('Error deleting order:', error);
      }
    },
    viewOrder(id) {
      this.$router.push({ name: 'admin.orders.show', params: { id: id } });
    },
    prevPage() {
      if (this.currentPage > 1) {
        this.currentPage--;
        this.fetchOrders();
      }
    },
    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
        this.fetchOrders();
      }
    },
    goToPage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
        this.fetchOrders();
      }
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