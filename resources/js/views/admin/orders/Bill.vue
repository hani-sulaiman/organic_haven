<template>
    <div class="bill-container" id="bill">
      <div class="bill-header">
        <h1>Order Bill</h1>
        <p>Order #{{ order.id }}</p>
      </div>
      <div class="bill-details">
        <p><strong>User:</strong> {{ order.user.name }}</p>
        <p><strong>Email:</strong> {{ order.user.email }}</p>
        <p><strong>Status:</strong> {{ order.status }}</p>
        <p><strong>Total:</strong> ${{ order.total.toFixed(2) }}</p>
        <p><strong>Created At:</strong> {{ order.created_at }}</p>
      </div>
      <table class="bill-table">
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
      <div class="bill-total">
        <p><strong>Subtotal:</strong> ${{ order.subtotal.toFixed(2) }}</p>
        <p><strong>Tax:</strong> ${{ order.tax.toFixed(2) }}</p>
        <p><strong>Grand Total:</strong> ${{ order.total.toFixed(2) }}</p>
      </div>
      <button @click="closeBill" class="btn btn-secondary btn-sm mt-3"><i class="fas fa-times"></i> Close</button>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      order: {
        type: Object,
        required: true,
      },
    },
    methods: {
      closeBill() {
        this.$emit('close-bill');
      },
    },
  };
  </script>
  
  <style scoped>
  .bill-container {
    max-width: 210mm; /* A4 width */
    margin: 0 auto;
    background-color: #ffffff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }
  
  .bill-header {
    text-align: center;
    margin-bottom: 20px;
  }
  
  .bill-header h1 {
    margin: 0;
    font-size: 2em;
    color: #333;
  }
  
  .bill-details {
    margin-bottom: 20px;
  }
  
  .bill-details p {
    margin: 5px 0;
    font-size: 1em;
    color: #555;
  }
  
  .bill-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
  }
  
  .bill-table th, .bill-table td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: left;
  }
  
  .bill-table th {
    background-color: #f8f9fa;
    font-weight: bold;
  }
  
  .bill-table tr:nth-child(even) {
    background-color: #f9f9f9;
  }
  
  .bill-total {
    text-align: right;
    font-size: 1.2em;
    color: #333;
  }
  
  .bill-total strong {
    font-size: 1.5em;
  }
  
  @media print {
    body * {
      visibility: hidden;
    }
  
    #bill, #bill * {
      visibility: visible;
    }
  
    #bill {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      margin: 0;
      padding: 10mm;
      box-shadow: none;
    }
  
    .bill-header h1 {
      font-size: 2.5em;
    }
  
    .bill-details p {
      font-size: 1.2em;
    }
  
    .bill-table {
      width: 100%;
      border-collapse: collapse;
    }
  
    .bill-table th, .bill-table td {
      border: 1px solid #000000;
      padding: 8px;
      text-align: left;
    }
  
    .bill-table th {
      background-color: #f8f9fa;
      font-weight: bold;
    }
  
    .bill-total {
      font-size: 1.4em;
    }
  
    .bill-total strong {
      font-size: 1.8em;
    }
  
    .btn {
      display: none;
    }
  }
  </style>