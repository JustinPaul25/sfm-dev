<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';

// Placeholder: get cage id from URL
const page = usePage();
const cageId = page.url.split('/').filter(Boolean).pop();

const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Cages', href: '/cages' },
  { title: `View Cage #${cageId}`, href: `/cages/${cageId}/view` },
];
</script>

<template>
  <Head :title="`View Cage #${cageId}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="max-w-2xl mx-auto p-6 bg-white dark:bg-gray-900 rounded-xl shadow">
      <div class="mb-4 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Cage #{{ cageId }}</h1>
        <Link href="/cages">
          <Button variant="secondary">Back to Cages</Button>
        </Link>
      </div>
      <div class="space-y-6">
        <div>
          <h2 class="text-lg font-semibold mb-2">Tilapia Size</h2>
          <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                  <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Date Tested</th>
                  <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Size (cm)</th>
                  <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Weight (g)</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr>
                  <td class="px-4 py-2 text-sm">2024-03-15</td>
                  <td class="px-4 py-2 text-sm">12.5</td>
                  <td class="px-4 py-2 text-sm">150</td>
                </tr>
                <tr>
                  <td class="px-4 py-2 text-sm">2024-03-10</td>
                  <td class="px-4 py-2 text-sm">11.8</td>
                  <td class="px-4 py-2 text-sm">135</td>
                </tr>
                <tr>
                  <td class="px-4 py-2 text-sm">2024-03-05</td>
                  <td class="px-4 py-2 text-sm">11.2</td>
                  <td class="px-4 py-2 text-sm">125</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div>
          <h2 class="text-lg font-semibold mb-2">Number of Fingerlings and Feed Type</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
              <h3 class="text-lg font-medium mb-2">Number of Fingerlings</h3>
              <p class="text-2xl font-bold text-primary">1,000</p>
            </div>
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
              <h3 class="text-lg font-medium mb-2">Feed Type</h3>
              <p class="text-lg">Premium Grower Feed</p>
              <p class="text-sm text-gray-500">Brand: AquaPro</p>
            </div>
          </div>
        </div>
        <div>
          <h2 class="text-lg font-semibold mb-2">Feeding Schedule</h2>
          <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                  <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Time</th>
                  <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Feed Amount (kg)</th>
                  <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Weather</th>
                  <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Notes</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr>
                  <td class="px-4 py-2 text-sm">06:00 AM</td>
                  <td class="px-4 py-2 text-sm">2.5</td>
                  <td class="px-4 py-2 text-sm">☀️ Clear</td>
                  <td class="px-4 py-2 text-sm">Morning feeding</td>
                </tr>
                <tr>
                  <td class="px-4 py-2 text-sm">11:00 AM</td>
                  <td class="px-4 py-2 text-sm">2.0</td>
                  <td class="px-4 py-2 text-sm">☁️ Cloudy</td>
                  <td class="px-4 py-2 text-sm">Midday feeding</td>
                </tr>
                <tr>
                  <td class="px-4 py-2 text-sm">03:00 PM</td>
                  <td class="px-4 py-2 text-sm">2.5</td>
                  <td class="px-4 py-2 text-sm">☀️ Clear</td>
                  <td class="px-4 py-2 text-sm">Afternoon feeding</td>
                </tr>
                <tr>
                  <td class="px-4 py-2 text-sm">06:00 PM</td>
                  <td class="px-4 py-2 text-sm">2.0</td>
                  <td class="px-4 py-2 text-sm">🌙 Clear</td>
                  <td class="px-4 py-2 text-sm">Evening feeding</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template> 