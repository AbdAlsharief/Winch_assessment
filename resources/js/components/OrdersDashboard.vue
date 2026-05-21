<template>
  <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">لوحة إدارة الطلبات</h1>
        <p class="mt-2 text-sm text-gray-600">إدارة وإسناد الطلبات للسائقين المتاحين</p>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
              <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">إجمالي الطلبات</dt>
                <dd class="text-2xl font-semibold text-gray-900">{{ orders.length }}</dd>
              </dl>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
              <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">قيد الانتظار</dt>
                <dd class="text-2xl font-semibold text-gray-900">{{ pendingCount }}</dd>
              </dl>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
              <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">تم الإسناد</dt>
                <dd class="text-2xl font-semibold text-gray-900">{{ assignedCount }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="flex flex-wrap gap-4 items-center">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
            <select 
              v-model="statusFilter" 
              @change="fetchOrders"
              class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
            >
              <option value="">الكل</option>
              <option value="pending">قيد الانتظار</option>
              <option value="assigned">تم الإسناد</option>
              <option value="completed">مكتمل</option>
            </select>
          </div>
          
          <button 
            @click="fetchOrders" 
            :disabled="loading"
            class="mt-6 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            تحديث
          </button>
        </div>
      </div>

      <!-- Notification -->
      <transition name="fade">
        <div 
          v-if="notification.show" 
          :class="[
            'mb-6 rounded-lg p-4',
            notification.type === 'success' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'
          ]"
        >
          <div class="flex">
            <div class="flex-shrink-0">
              <svg v-if="notification.type === 'success'" class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <svg v-else class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p :class="notification.type === 'success' ? 'text-green-800' : 'text-red-800'" class="text-sm font-medium">
                {{ notification.message }}
              </p>
            </div>
            <div class="ml-auto pl-3">
              <button @click="notification.show = false" class="inline-flex text-gray-400 hover:text-gray-500">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </transition>

      <!-- Orders Table -->
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <div v-if="loading && orders.length === 0" class="text-center py-12">
          <svg class="animate-spin h-12 w-12 text-blue-500 mx-auto" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <p class="mt-4 text-gray-500">جاري التحميل...</p>
        </div>

        <div v-else-if="orders.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد طلبات</h3>
          <p class="mt-1 text-sm text-gray-500">لا توجد طلبات متاحة حالياً</p>
        </div>

        <table v-else class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                رقم الطلب
              </th>
              <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                موقع الاستلام
              </th>
              <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                الحالة
              </th>
              <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                السائق
              </th>
              <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                الإجراءات
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                #{{ order.id }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div class="flex items-center">
                  <svg class="h-4 w-4 text-gray-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  {{ order.pickup_location.latitude.toFixed(4) }}, {{ order.pickup_location.longitude.toFixed(4) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(order.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ getStatusLabel(order.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span v-if="order.driver" class="flex items-center">
                  <svg class="h-4 w-4 text-gray-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  {{ order.driver.name }}
                </span>
                <span v-else class="text-gray-400">غير مسند</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button
                  v-if="order.status === 'pending'"
                  @click="assignOrder(order.id)"
                  :disabled="assigningOrders[order.id]"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg v-if="assigningOrders[order.id]" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  إسناد
                </button>
                <button
                  v-else-if="order.status === 'assigned'"
                  @click="unassignOrder(order.id)"
                  :disabled="assigningOrders[order.id]"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg v-if="assigningOrders[order.id]" class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-700" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  إلغاء الإسناد
                </button>
                <span v-else class="text-green-600 font-medium">مكتمل</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

// State
const orders = ref([]);
const loading = ref(false);
const assigningOrders = ref({});
const statusFilter = ref('');
const notification = ref({
  show: false,
  type: 'success',
  message: ''
});

// Computed
const pendingCount = computed(() => 
  orders.value.filter(order => order.status === 'pending').length
);

const assignedCount = computed(() => 
  orders.value.filter(order => order.status === 'assigned').length
);

// Methods
const fetchOrders = async () => {
  loading.value = true;
  try {
    const params = {};
    if (statusFilter.value) {
      params.status = statusFilter.value;
    }
    
    // For demo purposes, we'll fetch all orders
    // In production, you'd have a dedicated endpoint
    const response = await axios.get('/api/orders', { params });
    orders.value = response.data.data || response.data;
  } catch (error) {
    showNotification('error', 'فشل في تحميل الطلبات');
    console.error('Error fetching orders:', error);
  } finally {
    loading.value = false;
  }
};

const assignOrder = async (orderId) => {
  assigningOrders.value[orderId] = true;
  
  try {
    const response = await axios.post(`/api/orders/${orderId}/assign`);
    
    // Update the order in the list
    const index = orders.value.findIndex(o => o.id === orderId);
    if (index !== -1) {
      orders.value[index] = response.data.data || response.data;
    }
    
    showNotification('success', `تم إسناد الطلب #${orderId} بنجاح`);
  } catch (error) {
    let message = 'فشل في إسناد الطلب';
    
    if (error.response) {
      switch (error.response.status) {
        case 404:
          message = 'الطلب غير موجود';
          break;
        case 409:
          message = 'الطلب مسند بالفعل';
          break;
        case 503:
          message = 'لا يوجد سائقين متاحين حالياً';
          break;
        default:
          message = error.response.data.message || message;
      }
    }
    
    showNotification('error', message);
    console.error('Error assigning order:', error);
  } finally {
    assigningOrders.value[orderId] = false;
  }
};

const unassignOrder = async (orderId) => {
  assigningOrders.value[orderId] = true;
  
  try {
    const response = await axios.post(`/api/orders/${orderId}/unassign`);
    
    // Update the order in the list
    const index = orders.value.findIndex(o => o.id === orderId);
    if (index !== -1) {
      orders.value[index] = response.data.data || response.data;
    }
    
    showNotification('success', `تم إلغاء إسناد الطلب #${orderId} بنجاح`);
  } catch (error) {
    showNotification('error', error.response?.data?.message || 'فشل في إلغاء الإسناد');
    console.error('Error unassigning order:', error);
  } finally {
    assigningOrders.value[orderId] = false;
  }
};

const showNotification = (type, message) => {
  notification.value = {
    show: true,
    type,
    message
  };
  
  setTimeout(() => {
    notification.value.show = false;
  }, 5000);
};

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    assigned: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status) => {
  const labels = {
    pending: 'قيد الانتظار',
    assigned: 'تم الإسناد',
    completed: 'مكتمل'
  };
  return labels[status] || status;
};

// Lifecycle
onMounted(() => {
  fetchOrders();
});
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
