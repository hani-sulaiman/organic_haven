<template>
  <div class="dashboard">
    <h1>Dashboard</h1>
    <div class="statistics">
      <div class="card" v-for="(value, key) in statistics" :key="key">
        <div class="icon" :style="{ color: getIconColor(key) }">
          <i :class="getIconClass(key)"></i>
        </div>
        <div class="content">
          <h2>{{ formatKey(key) }}</h2>
          <p class="number">{{ value }}</p>
        </div>
      </div>
    </div>
    <div class="recent-orders">
      <h2>Recent Orders</h2>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>User</th>
            <th>Status</th>
            <th>Total</th>
            <th>Created At</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="order in orders.data" :key="order.id">
            <td>{{ order.id }}</td>
            <td>{{ order.user.name }}</td>
            <td>{{ order.status }}</td>
            <td>{{ order.total }}</td>
            <td>{{ order.created_at }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import apiClient from '../../api/index';

export default {
  data() {
    return {
      statistics: {},
      orders: {
        data: [],
      },
      iconMap: {
        user_count: 'fas fa-users',
        product_count: 'fas fa-boxes',
        category_count: 'fas fa-folder-open',
        orders_this_month: 'fas fa-calendar-alt',
        orders_this_year: 'fas fa-calendar',
        orders_all: 'fas fa-list',
        interactions_count: 'fas fa-handshake',
        last_data_trained_count: 'fas fa-database',
        last_trained_at: 'fas fa-clock',
      },
      iconColors: {
        user_count: '#007bff',
        product_count: '#28a745',
        category_count: '#ffc107',
        orders_this_month: '#dc3545',
        orders_this_year: '#6c757d',
        orders_all: '#17a2b8',
        interactions_count: '#fd7e14',
        last_data_trained_count: '#6610f2',
        last_trained_at: '#20c997',
      },
    };
  },
  created() {
    this.fetchStatistics();
    this.fetchOrders();
  },
  methods: {
    async fetchStatistics() {
      try {
        const response = await apiClient.get('/admin/statistics');
        this.statistics = response.data.data;
      } catch (error) {
        console.error('Error fetching statistics:', error);
      }
    },
    async fetchOrders() {
      try {
        const response = await apiClient.get('/admin/orders');
        this.orders = response.data.data;
      } catch (error) {
        console.error('Error fetching orders:', error);
      }
    },
    formatKey(key) {
      return key.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    },
    getIconClass(key) {
      return this.iconMap[key] || 'fas fa-chart-bar';
    },
    getIconColor(key) {
      return this.iconColors[key] || '#007bff';
    },
  },
};
</script>

<style scoped>
.dashboard {
  padding: 20px;
  background-color: #f4f4f9;
}

.statistics {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  justify-content: space-between;
}
.statistics .content{
  display: flex;
    flex-direction: column;
    align-items: center;
}

.card {
  background-color: #ffffff;
  border: 1px solid #ddd;
  border-radius: 10px;
  padding: 20px;
  width: calc(33.33% - 20px);
  box-sizing: border-box;
  display: flex;
  align-items: center;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s;
}

.card:hover {
  transform: translateY(-5px);
}

.card .icon {
  margin-right: 15px;
}

.card .icon i {
  font-size: 32px;
}

.card .content h2 {
  margin: 0;
  font-size: 1.2em;
  color: #333;
}

.card .content .number {
  margin: 5px 0 0;
  color: #333;
  font-size: 2em;
  font-weight: bold;
}

.recent-orders {
  margin-top: 40px;
  background-color: #ffffff;
  border: 1px solid #ddd;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.recent-orders h2 {
  margin-top: 0;
  font-size: 1.5em;
  color: #333;
}

.recent-orders table {
  width: 100%;
  border-collapse: collapse;
}

.recent-orders table th, .recent-orders table td {
  border: 1px solid #ddd;
  padding: 12px;
  text-align: left;
}

.recent-orders table th {
  background-color: #f8f9fa;
  font-weight: bold;
}

.recent-orders table tr:nth-child(even) {
  background-color: #f9f9f9;
}

.recent-orders table tr:hover {
  background-color: #f1f1f1;
}
</style>