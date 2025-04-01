<template>
    <div class="container mt-4">
      <h1 class="mb-4">AI Model Training</h1>
      <div class="card">
        <div class="card-header bg-primary text-white">
          <i class="fas fa-brain mr-2"></i> Model Training Status
        </div>
        <div class="card-body">
          <div v-if="isTraining" class="training-animation">
            <i class="fas fa-cog fa-spin fa-5x text-primary"></i>
            <h2 class="mt-3">Training the AI Model...</h2>
            <p class="lead">Please wait while the model is being trained.</p>
          </div>
          <div v-else>
            <button @click="trainModelAsync" class="btn btn-primary btn-lg">
              <i class="fas fa-play-circle mr-2"></i> Train AI Model
            </button>
            <div v-if="trainingMessage" class="alert alert-info mt-3">
              <i class="fas fa-info-circle mr-2"></i> {{ trainingMessage }}
            </div>
            <div v-if="errorMessage" class="alert alert-danger mt-3">
              <i class="fas fa-exclamation-triangle mr-2"></i> {{ errorMessage }}
            </div>
            <div v-if="lastTrainedAt" class="alert alert-success mt-3">
              <i class="fas fa-check-circle mr-2"></i> Last Trained At: {{ lastTrainedAt }}
            </div>
            <div v-if="dataTrainedCount" class="alert alert-success mt-3">
              <i class="fas fa-database mr-2"></i> Data Trained Count: {{ dataTrainedCount }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import apiClient from '@/api/index';
  
  export default {
    data() {
      return {
        isTraining: false,
        trainingMessage: '',
        errorMessage: '',
        lastTrainedAt: null,
        dataTrainedCount: null,
      };
    },
    created() {
      this.fetchTrainingStatus();
    },
    methods: {
      async fetchTrainingStatus() {
        try {
          const response = await apiClient.get('/admin/statistics');
          if (response.data.success) {
            this.lastTrainedAt = response.data.data.last_trained_at;
            this.dataTrainedCount = response.data.data.last_data_trained_count;
          }
        } catch (error) {
          console.error('Error fetching training status:', error);
        }
      },
      async trainModelAsync() {
        this.isTraining = true;
        this.trainingMessage = '';
        this.errorMessage = '';
  
        try {
          const response = await apiClient.post('/admin/train-model');
          if (response.data.success) {
            this.trainingMessage = response.data.message;
            this.fetchTrainingStatus(); // Refresh training status after successful training
          } else {
            this.errorMessage = response.data.message;
          }
        } catch (error) {
          this.errorMessage = error.response?.data?.message || 'An error occurred during model training.';
        } finally {
          this.isTraining = false;
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .container {
    max-width: 1200px;
  }
  
  .card {
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }
  
  .card-header {
    font-size: 1.5em;
    padding: 15px;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
  }
  
  .card-body {
    padding: 30px;
  }
  
  .training-animation {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }
  
  .training-animation i {
    color: #007bff;
  }
  
  .training-animation h2 {
    margin-top: 20px;
    font-size: 2em;
    color: #333;
  }
  
  .training-animation p {
    margin-top: 10px;
    font-size: 1.2em;
    color: #555;
  }
  
  .alert {
    margin-top: 20px;
    padding: 15px;
    border-radius: 5px;
    text-align: center;
  }
  
  .alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
  }
  
  .alert-danger {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
  }
  
  .alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
  }
  </style>