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
  samples_count?: number;
  investor?: {
    id: number;
    name: string;
  };
}

interface PaginationData {
  data: Sampling[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number;
  to: number;
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
const isLoading = computed(() => store.loading);
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

const samplings = computed(() => {
  const data = store.samplings as any;
  return data?.data || [];
});
const pagination = computed(() => {
  const data = store.samplings as any;
  return data?.current_page ? {
    current_page: data.current_page,
    last_page: data.last_page,
    per_page: data.per_page,
    total: data.total,
    from: data.from,
    to: data.to
  } : null;
});

function handleSearch() {
  store.setFilters({ search: search.value, page: 1 });
  store.fetchSamplings();
}

// Auto-search on input change with debounce
let searchTimeout: number;
function handleSearchInput() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    handleSearch();
  }, 300);
}

// Pagination functions
function goToPage(page: number) {
  store.setFilters({ ...store.filters, page });
  store.fetchSamplings();
}

function nextPage() {
  if (pagination.value && pagination.value.current_page < pagination.value.last_page) {
    goToPage(pagination.value.current_page + 1);
  }
}

function prevPage() {
  if (pagination.value && pagination.value.current_page > 1) {
    goToPage(pagination.value.current_page - 1);
  }
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

async function generateSamples(samplingId: number) {
  try {
    const result = await Swal.fire({
      title: 'Generate Samples?',
      text: 'This will create 30 sample records for this sampling. Continue?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes, Generate',
      cancelButtonText: 'Cancel'
    });

    if (result.isConfirmed) {
      await store.generateSamples(samplingId);
      await store.fetchSamplings();
      Swal.fire({ 
        icon: 'success', 
        title: 'Samples Generated!', 
        text: '30 sample records have been created successfully.' 
      });
    }
  } catch (error: any) {
    if (error?.response?.status === 400) {
      Swal.fire({ 
        icon: 'warning', 
        title: 'Samples Already Exist', 
        text: error.response.data.message 
      });
    } else {
      Swal.fire({ 
        icon: 'error', 
        title: 'Error', 
        text: error?.message || 'Failed to generate samples.' 
      });
    }
  }
}

function exportSamplingReport(samplingId: number) {
  // Open the export URL in a new tab
  window.open(route('samplings.export-report', samplingId), '_blank');
}

function printSamplingReport(samplingId: number) {
  // Open the sampling report page in a new tab for printing
  window.open(route('samplings.report', { sampling: samplingId }), '_blank');
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
          <Input v-model="search" placeholder="Search samplings..." @input="handleSearchInput" class="w-64" />
          <Button @click="handleSearch" variant="default" :disabled="isLoading">
            {{ isLoading ? 'Searching...' : 'Search' }}
          </Button>
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
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Samples</th>
              <th class="px-6 py-3"></th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-if="isLoading">
              <td colspan="6" class="px-6 py-4 text-center text-gray-500">Loading samplings...</td>
            </tr>
            <tr v-else-if="!samplings || !Array.isArray(samplings) || samplings.length === 0">
              <td colspan="6" class="px-6 py-4 text-center text-gray-500">No samplings found.</td>
            </tr>
            <tr v-else v-for="s in samplings" :key="s?.id">
              <td class="px-6 py-4 whitespace-nowrap">
                {{ s?.investor?.name || investors.find(i => i.id === s?.investor_id)?.name || s?.investor_id }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">{{ s?.date_sampling }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ s?.doc }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ s?.cage_no }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                  {{ s?.samples_count || 0 }} samples
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex gap-1">
                  <Button 
                    variant="outline" 
                    size="sm" 
                    @click="generateSamples(s?.id)" 
                    :disabled="!s?.id"
                    title="Generate Samples"
                    class="w-8 h-8 p-0"
                  >
                    📊
                  </Button>
                  <Button 
                    variant="outline" 
                    size="sm" 
                    @click="printSamplingReport(s?.id)" 
                    :disabled="!s?.id"
                    title="Print Report"
                    class="w-8 h-8 p-0"
                  >
                    🖨️
                  </Button>
                  <Button 
                    variant="outline" 
                    size="sm" 
                    @click="exportSamplingReport(s?.id)" 
                    :disabled="!s?.id"
                    title="Export to Excel"
                    class="w-8 h-8 p-0"
                  >
                    📄
                  </Button>
                  <Button 
                    variant="secondary" 
                    size="sm" 
                    @click="openEditDialog(s)" 
                    :disabled="!s?.id"
                    title="Update"
                    class="w-8 h-8 p-0"
                  >
                    ✏️
                  </Button>
                  <Button 
                    variant="destructive" 
                    size="sm" 
                    @click="confirmDelete(s?.id)" 
                    :disabled="!s?.id"
                    title="Delete"
                    class="w-8 h-8 p-0"
                  >
                    🗑️
                  </Button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination" class="flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center text-sm text-gray-700 dark:text-gray-300">
          <span>Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results</span>
        </div>
        <div class="flex items-center space-x-2">
          <Button 
            variant="outline" 
            size="sm" 
            @click="prevPage" 
            :disabled="pagination.current_page === 1"
          >
            Previous
          </Button>
          <div class="flex items-center space-x-1">
            <template v-for="page in Math.min(5, pagination.last_page)" :key="page">
              <Button 
                v-if="page === 1 || page === pagination.last_page || (page >= pagination.current_page - 2 && page <= pagination.current_page + 2)"
                variant="outline" 
                size="sm" 
                @click="goToPage(page)"
                :class="page === pagination.current_page ? 'bg-primary text-primary-foreground' : ''"
              >
                {{ page }}
              </Button>
              <span v-else-if="page === pagination.current_page - 3 || page === pagination.current_page + 3" class="px-2">...</span>
            </template>
          </div>
          <Button 
            variant="outline" 
            size="sm" 
            @click="nextPage" 
            :disabled="pagination.current_page === pagination.last_page"
          >
            Next
          </Button>
        </div>
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
