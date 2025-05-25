<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useSamplingStore } from '@/Stores/SamplingStore';
import { useInvestorStore } from '@/Stores/InvestorStore';
import { Head } from '@inertiajs/vue3';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogTrigger from '@/components/ui/dialog/DialogTrigger.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';
import Swal from 'sweetalert2';

// TODO: Replace with actual investor list from API or store

interface Sampling {
  id: number;
  investor_id: number;
  date_sampling: string;
  doc: string;
  cage_no: string;
}

interface Investor {
  id: number;
  name: string;
}

const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Samplings', href: '/samplings' },
];

const store = useSamplingStore();
const investorStore = useInvestorStore();
const search = ref('');
const showCreateDialog = ref(false);
const showDeleteDialog = ref(false);
const deleteTargetId = ref<number|null>(null);
const showEditDialog = ref(false);
const editSampling = ref<Sampling | null>(null);
const investors = computed(() => investorStore.investorsSelect as Investor[]);

const newSampling = ref<Pick<Sampling, 'investor_id' | 'date_sampling' | 'doc' | 'cage_no'>>({
  investor_id: investors.value[0]?.id || 3,
  date_sampling: '',
  doc: '',
  cage_no: '',
});

const samplings = computed(() => store.samplings as Sampling[]);

const filteredSamplings = computed<Sampling[]>(() => {
  if (!search.value) return samplings.value;
  return samplings.value.filter(s =>
    String(s.investor_id).includes(search.value.toLowerCase()) ||
    s.date_sampling.toLowerCase().includes(search.value.toLowerCase()) ||
    s.doc.toLowerCase().includes(search.value.toLowerCase()) ||
    s.cage_no.toLowerCase().includes(search.value.toLowerCase())
  );
});

function handleSearch() {
  store.setFilters({ search: search.value });
  store.fetchSamplings();
}

function openCreateDialog() {
  newSampling.value = {
    investor_id: investors.value[0]?.id || 1,
    date_sampling: '',
    doc: '',
    cage_no: '',
  };
  showCreateDialog.value = true;
}

async function createSampling() {
  try {
    await store.createSampling(newSampling.value);
    await store.fetchSamplings();
    showCreateDialog.value = false;
    Swal.fire({ icon: 'success', title: 'Sampling created successfully!' });
  } catch (error: any) {
    Swal.fire({ icon: 'error', title: 'Error', text: error?.message || 'Failed to create sampling.' });
  }
}

function confirmDelete(id: number) {
  deleteTargetId.value = id;
  showDeleteDialog.value = true;
}

async function deleteSampling() {
  if (deleteTargetId.value !== null) {
    try {
      await store.deleteSampling(deleteTargetId.value);
      await store.fetchSamplings();
      showDeleteDialog.value = false;
      deleteTargetId.value = null;
      Swal.fire({ icon: 'success', title: 'Sampling deleted successfully!' });
    } catch (error: any) {
      Swal.fire({ icon: 'error', title: 'Error', text: error?.message || 'Failed to delete sampling.' });
    }
  }
}

function openEditDialog(s: Sampling) {
  editSampling.value = { ...s };
  showEditDialog.value = true;
}

async function updateSamplingHandler() {
  if (editSampling.value && editSampling.value.id) {
    try {
      await store.updateSampling(editSampling.value.id, {
        investor_id: editSampling.value.investor_id,
        date_sampling: editSampling.value.date_sampling,
        doc: editSampling.value.doc,
        cage_no: editSampling.value.cage_no,
      });
      await store.fetchSamplings();
      showEditDialog.value = false;
      editSampling.value = null;
      Swal.fire({ icon: 'success', title: 'Sampling updated successfully!' });
    } catch (error: any) {
      Swal.fire({ icon: 'error', title: 'Error', text: error?.message || 'Failed to update sampling.' });
    }
  }
}

onMounted(() => {
  store.fetchSamplings();
  investorStore.fetchInvestorsSelect();
});
</script>

<template>
  <Head title="Samplings" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-4 p-4">
      <div class="flex items-center justify-between gap-2">
        <div class="flex gap-2 items-center">
          <Input v-model="search" placeholder="Search samplings..." @keyup.enter="handleSearch" class="w-64" />
          <Button @click="handleSearch" variant="default">Search</Button>
        </div>
        <Button @click="openCreateDialog" variant="secondary">Create Sampling</Button>
      </div>
      <div class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-white dark:bg-gray-900">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Investor</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Sampling</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DOC</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cage No</th>
              <th class="px-6 py-3"></th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-if="!samplings || !Array.isArray(samplings.data) || samplings.data.length === 0">
              <td colspan="5" class="px-6 py-4 text-center text-gray-500">No samplings found.</td>
            </tr>
            <tr v-else v-for="s in samplings.data" :key="s?.id">
              <td class="px-6 py-4 whitespace-nowrap">
                {{ investors.find(i => i.id === s?.investor_id)?.name || s?.investor_id }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">{{ s?.date_sampling }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ s?.doc }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ s?.cage_no }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <Button variant="secondary" size="sm" @click="openEditDialog(s)" :disabled="!s?.id">Update</Button>
                <Button variant="destructive" size="sm" @click="confirmDelete(s?.id)" :disabled="!s?.id">Delete</Button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create Sampling Dialog -->
    <Dialog v-model:open="showCreateDialog">
      <DialogTrigger as-child />
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Create Sampling</DialogTitle>
        </DialogHeader>
        <form @submit.prevent="createSampling" class="flex flex-col gap-4 mt-2">
          <select v-model="newSampling.investor_id" class="input w-full rounded border p-2" required>
            <option v-for="inv in investors" :key="inv.id" :value="inv.id" class="bg-background text-foreground">{{ inv.name }}</option>
          </select>
          <Input v-model="newSampling.date_sampling" type="date" placeholder="Date Sampling" required />
          <Input v-model="newSampling.doc" placeholder="DOC" required />
          <Input v-model="newSampling.cage_no" placeholder="Cage No" required />
          <DialogFooter class="flex justify-end gap-2 mt-4">
            <Button type="button" variant="secondary" @click="showCreateDialog = false">Cancel</Button>
            <Button type="submit" variant="default">Create</Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>

    <!-- Delete Confirmation Dialog -->
    <Dialog v-model:open="showDeleteDialog">
      <DialogTrigger as-child />
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Delete Sampling</DialogTitle>
        </DialogHeader>
        <div class="mt-2">Are you sure you want to delete this sampling?</div>
        <DialogFooter class="flex justify-end gap-2 mt-4">
          <Button type="button" variant="secondary" @click="showDeleteDialog = false">Cancel</Button>
          <Button type="button" variant="destructive" @click="deleteSampling">Delete</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Edit Sampling Dialog -->
    <Dialog v-model:open="showEditDialog" v-if="editSampling">
      <DialogTrigger as-child />
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Edit Sampling</DialogTitle>
        </DialogHeader>
        <form @submit.prevent="updateSamplingHandler" class="flex flex-col gap-4 mt-2">
          <select v-model="editSampling.investor_id" class="input w-full rounded border p-2" required>
            <option v-for="inv in investors" :key="inv.id" :value="inv.id" class="bg-background text-foreground">{{ inv.name }}</option>
          </select>
          <Input v-model="editSampling.date_sampling" type="date" placeholder="Date Sampling" required />
          <Input v-model="editSampling.doc" placeholder="DOC" required />
          <Input v-model="editSampling.cage_no" placeholder="Cage No" required />
          <DialogFooter class="flex justify-end gap-2 mt-4">
            <Button type="button" variant="secondary" @click="showEditDialog = false">Cancel</Button>
            <Button type="submit" variant="default">Update</Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>
