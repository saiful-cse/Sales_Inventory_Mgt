<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12"></div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <h3>Customer</h3>
                        </div>
                        <hr />
                        <div class="float-end">
                            <a href="/customer_save_page?id=0" class="btn btn-success mx-3 btn-sm">
                                Add Customer
                            </a>
                        </div>

                        <!-- Modal -->
                        <div>
                            <input placeholder="Search..." class="form-control mb-2 w-auto form-control-sm" type="text"
                                v-model="searchValue">
                            <EasyDataTable buttons-pagination alternating :headers="Header" :items="Item"
                                :rows-per-page="10" :search-field="searchField" :search-value="searchValue">
                                <template #item-action="{ id }">
                                    <Link class="btn btn-success mx-3 btn-sm" :href="`/customer_save_page?id=${id}`">Edit
                                    </Link>
                                    <button class="btn btn-danger btn-sm" @click="DeleteClick(id)">Delete</button>
                                </template>
                            </EasyDataTable>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm, Link, usePage, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { createToaster } from '@meforma/vue-toaster';

const toaster = createToaster({
    position: 'top-right'
});

const Header = [
    { text: 'Name', value: 'name' },
    { text: 'Email', value: 'email' },
    { text: 'Mobile', value: 'mobile' },
    { text: 'Actions', value: 'action' }
];

const page = usePage();
const Item = ref(page.props.customers);

const searchField = ref();
const searchValue = ref();

const DeleteClick = (id) => {
    let text = 'Are you sure you want to delete this customer?';
    if (confirm(text) === true) {
        router.get(`/customer_delete/${id}`);
        toaster.success('Customer deleted successfully');
    }else{
        toaster.info('Customer deletion canceled');
    }
};

</script>
