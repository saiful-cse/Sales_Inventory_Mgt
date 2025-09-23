<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <h3>Product</h3>
                        </div>
                        <hr />
                        <div class="float-end">
                            <a href="/product_save_page?id=0" class="btn btn-success mx-3 btn-sm">
                                Add Product
                            </a>
                        </div>
                        <div>
                            <input placeholder="Search..." class="form-control mb-2 w-auto form-control-sm" type="text"
                                v-model="searchValue">
                            <EasyDataTable buttons-pagination alternating :headers="Header" :items="Item" show-index
                                :rows-per-page="10" :search-field="searchField" :search-value="searchValue">

                                <template #item-img="{ image }" class="pt-2 pb-2">
                                    <img :src="image ? image : 'placeholderimage.png'" :alt="image" height="40px"
                                        width="40px">

                                </template>

                                <template #item-number="{ id }">
                                    <Link class="btn btn-success mx-3 btn-sm" :href="`/product_save_page?id=${id}`">Edit
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
import { Link, usePage, useForm, router } from '@inertiajs/vue3';
import { createToaster } from '@meforma/vue-toaster';
import { ref } from 'vue';

const toaster = createToaster({
    position: 'top-right'
});

const Header = [
    { text: 'Image', value: 'img' },
    { text: 'Category', value: 'category.name' },
    { text: 'Price', value: 'price' },
    { text: 'Quantity', value: 'unit' },
    { text: 'Action', value: 'number' }
];

const page = usePage();
const Item = ref(page.props.products);

const searchValue = ref('');

const DeleteClick = (id) =>{
    let text = 'Are you sure you want to delete this product?';
    if (confirm(text) === true) {
        router.get(`/product_delete/${id}`);
        toaster.success('Product deleted successfully');
    }else{
        toaster.info('Product deletion canceled');
    }
}
</script>
