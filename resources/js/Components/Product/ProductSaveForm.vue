<template>
    <div class="container-fluid">
        <div class="row d-flex justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="float-end">
                            <Link href="/product_page" class="btn btn-success mx-3 btn-sm">
                            Back
                            </Link>
                        </div>
                        <form @submit.prevent="submit" enctype="multipart/form-data">
                            <div class="card-body">
                                <h4>Save Product</h4>
                                <input id="id" name="id" hidden v-model="form.id" placeholder="Product ID"
                                    class="form-control" type="text" />
                                <br />
                                <input id="name" name="name" v-model="form.name" placeholder="Product Name"
                                    class="form-control" type="text" />
                                <br />
                                <input id="price" name="price" v-model="form.price" placeholder="Product Price"
                                    class="form-control" type="text" />
                                <br />
                                <input id="unit" name="unit" v-model="form.unit" placeholder="Product Unit"
                                    class="form-control" type="text" />
                                <br />
                                <!-- Category Dropdown -->
                                <div>
                                    <label for="category">Select Category:</label>
                                    <select v-model="form.category_id" class="form-control" id="category">
                                        <option value="" disabled>Select a category</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}</option>

                                    </select>
                                </div>
                                <br />
                                <div>
                                    <label for="image">Product Image:</label> <br>
                                    <ImageUpload :productImage="form.image" @image="(e) => form.image = e" />
                                </div>
                                <br />
                                <button type="submit" class="btn w-100 btn-success">Save Change</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, usePage, useForm, router } from '@inertiajs/vue3';
import { createToaster } from '@meforma/vue-toaster';
const toaster = createToaster({
    position: 'top-right'
});

import { ref } from 'vue';
import ImageUpload from './ImageUpload.vue';

const urlParam = new URLSearchParams(window.location.search);
let id = ref(parseInt(urlParam.get('id')));

const form = useForm({
    id: id.value,
    name: '',
    price: '',
    unit: '',
    category_id: '',
    image: null
});

const page = usePage();

let categories = page.props.categories;
let product = page.props.product;

let URL = '/product_create';

if (id.value !== 0 && product !== null) {
    URL = '/product_update';
    form.name = product.name;
    form.price = product.price;
    form.unit = product.unit;
    form.category_id = product.category_id;
    form.image = product.image;
}

function submit() {
    if (form.name.length === 0) {
        toaster.warning('Product name is required');
    } else if (form.price.length === 0) {
        toaster.warning('Product price is required');
    } else if (form.unit.length === 0) {
        toaster.warning('Product unit is required');
    } else if (form.category_id.length === 0) {
        toaster.warning('Product category is required');
    } else {
        form.post(URL), {
            onSuccess: () => {
                if (page.props.flash.status === true) {
                    toaster.success(page.props.flash.message);
                    router.get('/product_page');
                } else {
                    toaster.error(page.props.flash.message);
                }
            }
        }
    }
}

</script>
