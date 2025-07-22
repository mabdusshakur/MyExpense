<script setup>
import { router, useForm } from "@inertiajs/vue3";
import { Chart } from "chart.js/auto";
import { onMounted, ref, watch } from "vue";

const showAddModal = ref(false);
const showEditModal = ref(false);
const currentExpense = ref(null);
const expenseChartRef = ref(null);

const props = defineProps({
    expenses: Array,
    totalExpenses: Number,
    monthlyExpenses: Number,
    categories: Array
});

const form = useForm({
    description: "",
    amount: "",
    category: "",
    date: new Date().toISOString().split("T")[0], // Default to today's date
});

const submitExpense = () => {
    if (showEditModal.value) {
        form.put('/expenses/' + currentExpense.value.id)
    }
    else {
        form.post("/expenses");
    }
    closeModal();
};


const editExpense = (expense) => {
    form.description = expense.description;
    form.amount = expense.amount;
    form.category = expense.category;
    form.date = new Date(expense.date).toISOString().split("T")[0]; // Format date for input
    currentExpense.value = expense;
    showEditModal.value = true;
};

const logout = () => {
    router.post("/logout");
};

let chart = null;

const closeModal = () => {
    showAddModal.value = false;
    showEditModal.value = false;
    form.reset();
};

onMounted(() => {
    // Initialize the chart after the component is mounted
    initializeChart();
});

watch(() => props.categories, () => {
    if(chart){
        chart.destroy();
    }
    initializeChart();
});


const initializeChart = () => {
    const ctx = expenseChartRef.value.getContext('2d');
    const labels = Object.keys(props.categories);
    const data = Object.values(props.categories);

    console.log("Chart Data:", [data, labels]);
    chart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    '#4F46E5',
                    '#10B981',
                    '#F59E0B',
                    '#EF4444',
                    '#8B5CF6',
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Expenses by Category'
                }
            }
        }
    });
}
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-2xl font-bold text-white">
                                MyExpenses
                            </h1>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <button @click="logout"
                            class="text-white hover:text-indigo-100 font-medium transition-colors duration-200">
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="py-10">
            <!-- Header -->
            <header>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="md:flex md:items-center md:justify-between">
                        <div class="flex-1 min-w-0">
                            <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">
                                Welcome Back!
                            </h2>
                            <p class="mt-1 text-sm text-gray-500">
                                Track and manage your expenses easily
                            </p>
                        </div>
                        <div class="mt-4 flex md:mt-0 md:ml-4">
                            <button @click="showAddModal = true"
                                class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg text-base font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg transform transition-all duration-200 hover:scale-105">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add Expense
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <main>
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="px-4 py-8 sm:px-0">
                        <!-- Stats Cards -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-2">
                            <div
                                class="bg-white overflow-hidden shadow-lg rounded-2xl transition-transform duration-300 hover:scale-105">
                                <div class="px-6 py-6">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 p-3 bg-indigo-500 rounded-lg">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="ml-5">
                                            <dt class="text-sm font-medium text-gray-500 truncate">
                                                Total Expenses
                                            </dt>
                                            <dd class="mt-1 text-3xl font-bold text-gray-900">
                                                ${{ props.totalExpenses }}
                                            </dd>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="bg-white overflow-hidden shadow-lg rounded-2xl transition-transform duration-300 hover:scale-105">
                                <div class="px-6 py-6">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 p-3 bg-purple-500 rounded-lg">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div class="ml-5">
                                            <dt class="text-sm font-medium text-gray-500 truncate">
                                                Monthly Expenses
                                            </dt>
                                            <dd class="mt-1 text-3xl font-bold text-gray-900">
                                                ${{ props.monthlyExpenses }}
                                            </dd>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chart -->
                        <div class="mt-8 bg-white shadow-lg rounded-2xl">
                            <div class="px-6 py-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">
                                    Expense Distribution
                                </h3>
                                <div class="max-w-md mx-auto h-64">
                                    <canvas ref="expenseChartRef"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Expenses Table -->
                        <div class="mt-8">
                            <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
                                <div class="px-6 py-5 border-b border-gray-100">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        Recent Expenses
                                    </h3>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col"
                                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Date
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Description
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Category
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Amount
                                                </th>
                                                <th scope="col" class="relative px-6 py-4">
                                                    <span class="sr-only">Actions</span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-100">
                                            <tr class="hover:bg-gray-50 transition-colors duration-200"
                                                v-for="expense in props.expenses" :key="expense.id">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ new
                                                    Date(expense.date).toLocaleDateString() }}</td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ expense.description }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span
                                                        class="px-3 py-1 text-sm rounded-full bg-purple-100 text-purple-700">{{
                                                            expense.category }}</span>
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    ${{ expense.amount }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <button @click="editExpense(expense)"
                                                        class="inline-flex items-center px-3 py-1 rounded-md text-indigo-600 hover:text-indigo-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                        Edit
                                                    </button>
                                                    <button @click="form.delete('/expenses/' + expense.id)"
                                                        class="inline-flex items-center px-3 py-1 rounded-md text-red-600 hover:text-red-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- Add/Edit Modal -->
        <div v-if="showAddModal || showEditModal" id="addExpenseModal" class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <div
                    class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="submitExpense">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                                <input type="text" v-model="form.description"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    required />
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Amount</label>
                                <input type="number" step="0.01" v-model="form.amount"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    required />
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                                <input type="text" v-model="form.category"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    required />
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Date</label>
                                <input type="date" v-model="form.date"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    required />
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ showEditModal ? 'Update Expense' : 'Add Expense' }}
                            </button>
                            <button @click="closeModal" type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang=""></style>
