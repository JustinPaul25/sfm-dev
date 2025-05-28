<script setup>
import { ref, computed, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useFeedTypeStore } from '@/Stores/FeedTypeStore';
import { storeToRefs } from 'pinia';
import { Head } from '@inertiajs/vue3';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogTrigger from '@/components/ui/dialog/DialogTrigger.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';

const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Feed Types', href: '/feed-types' },
];

const store = useFeedTypeStore();
const { feedTypes, loading, error } = storeToRefs(store);

// Ensure feedTypes is always an array for template safety
const feedTypesSafe = computed(() => Array.isArray(feedTypes.value) ? feedTypes.value : []);

const showDialog = ref(false);
const isEdit = ref(false);
const form = ref({ id: null, feed_type: '', brand: '' });
const formError = ref(null);

onMounted(() => {
  store.fetchFeedTypes();
});

function openCreateDialog() {
  isEdit.value = false;
  form.value = { id: null, feed_type: '', brand: '' };
  showDialog.value = true;
  formError.value = null;
}

function openEditDialog(item) {
  isEdit.value = true;
  form.value = { ...item };
  showDialog.value = true;
  formError.value = null;
}

async function submitForm() {
  formError.value = null;
  try {
    if (isEdit.value) {
      await store.updateFeedType(form.value.id, {
        feed_type: form.value.feed_type,
        brand: form.value.brand,
      });
    } else {
      await store.createFeedType({
        feed_type: form.value.feed_type,
        brand: form.value.brand,
      });
    }
    showDialog.value = false;
  } catch (e) {
    formError.value = e?.response?.data?.errors || 'An error occurred.';
  }
}

async function deleteFeedType(id) {
  if (confirm('Are you sure you want to delete this feed type?')) {
    await store.deleteFeedType(id);
  }
}

async function restoreFeedType(id) {
  await store.restoreFeedType(id);
}
</script>

<template>
  <Head title="Feed Types" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-4 p-4">
      <div class="flex items-center justify-between gap-2">
        <h1 class="text-2xl font-bold">Feed Types</h1>
        <Button @click="openCreateDialog" variant="secondary">Add Feed Type</Button>
      </div>
      <div v-if="error" class="text-red-500 mb-2">{{ error }}</div>
      <div class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-white dark:bg-gray-900">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Feed Type</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deleted At</th>
              <th class="px-6 py-3"></th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-if="feedTypesSafe.length === 0">
              <td colspan="5" class="px-6 py-4 text-center text-gray-500">No feed types found.</td>
            </tr>
            <tr v-else v-for="item in feedTypesSafe" :key="item.id" :class="item.deleted_at ? 'bg-red-50 dark:bg-red-950/50' : ''">
              <td class="px-6 py-4 whitespace-nowrap">{{ item.id }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ item.feed_type }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ item.brand }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ item.deleted_at ? item.deleted_at : '-' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                <Button variant="secondary" size="sm" @click="openEditDialog(item)" :disabled="!!item.deleted_at">Edit</Button>
                <Button v-if="!item.deleted_at" variant="destructive" size="sm" @click="deleteFeedType(item.id)">Delete</Button>
                <Button v-else variant="default" size="sm" @click="restoreFeedType(item.id)">Restore</Button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Create/Edit Feed Type Dialog -->
      <Dialog v-model:open="showDialog">
        <DialogTrigger as-child />
        <DialogContent>
          <DialogHeader>
            <DialogTitle>{{ isEdit ? 'Edit' : 'Add' }} Feed Type</DialogTitle>
          </DialogHeader>
          <form @submit.prevent="submitForm" class="flex flex-col gap-4 mt-2">
            <div>
              <Input v-model="form.feed_type" placeholder="Feed Type" required />
              <div v-if="formError?.feed_type" class="text-red-500 text-sm mt-1">{{ formError.feed_type[0] }}</div>
            </div>
            <div>
              <Input v-model="form.brand" placeholder="Brand" required />
              <div v-if="formError?.brand" class="text-red-500 text-sm mt-1">{{ formError.brand[0] }}</div>
            </div>
            <DialogFooter class="flex justify-end gap-2 mt-4">
              <Button type="button" variant="secondary" @click="showDialog = false">Cancel</Button>
              <Button type="submit" variant="default" :disabled="loading">{{ isEdit ? 'Update' : 'Create' }}</Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>
    </div>
  </AppLayout>
</template> 