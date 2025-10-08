<template>
    <div class="container-fluid">
        <div class="row">
            <!-- Billing Selection -->
            <div class="col-md-4 col-lg-4 p-2">
                <div class="card">
                    <div class="card-body">
                        <h4>Billed To</h4>
                        <div class="shadow-sm h-100 bg-white rounded-3 p-3 mt-4">
                            <div class="row">
                                <div class="col-8">
                                    <span class="text-bold text-dark">BILLED TO</span>
                                    <p class="text-xs mx-0 my-1">Name: <span>{{ selectedCustomer ? selectedCustomer.name
                                        : '' }}</span></p>
                                    <p class="text-xs mx-0 my-1">Email: <span>{{ selectedCustomer ?
                                        selectedCustomer.email : '' }}</span></p>
                                    <p class="text-xs mx-0 my-1">User ID: <span>{{ selectedCustomer ?
                                        selectedCustomer.id : '' }}</span></p>
                                </div>
                                <div class="col-4">
                                    <p class="text-bold mx-0 my-1 text-dark">Invoice</p>
                                    <p class="text-xs mx-0 my-1">Date: {{ new Date().toISOString().slice(0, 10) }}</p>
                                </div>
                            </div>
                            <hr class="mx-0 my-2 p-0 bg-secondary" />
                            <div class="row">
                                <div class="col-12">
                                    <table class="table w-100">
                                        <thead>
                                            <tr class="text-xs">
                                                <th>Name</th>
                                                <th>Qty</th>
                                                <th>Total</th>
                                                <th>Remove</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="text-center" v-for="(product, index) in selectedProduct"
                                                :key="product.id || index">
                                                <td>{{ product.name }}</td>
                                                <td>{{ product.unit }}</td>
                                                <td>{{ product.price }}</td>
                                                <td>
                                                    <button class="" @click="removeQty(product.id)">-</button>
                                                    <button class="" @click="addQty(product.id)">+</button>
                                                    <button class="btn btn-danger btn-sm"
                                                        @click="removeProductFromSale(index)">Remove</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr class="mx-0 my-2 p-0 bg-secondary" />
                            <div class="row">
                                <div class="col-12">
                                    <p class="text-bold text-xs my-1 text-dark">Total: <i
                                            class="bi bi-currency-dollar"></i> {{ calculateTotal() }}</p>
                                    <p class="text-bold text-xs my-1 text-dark">VAT ({{ vatRate }}%): <i
                                            class="bi bi-currency-dollar"></i> {{ vatAmount }}</p>
                                    <p><button @click="applyVat"
                                            class="btn btn-info btn-sm my-1 bg-gradient-primary w-40">Apply
                                            VAT</button></p>
                                    <p><button @click="vatRemove"
                                            class="btn btn-secondary btn-sm my-1 bg-gradient-primary w-40">Remove
                                            VAT</button></p>

                                    <p><span class="text-xxs">Discount Mode:</span></p>
                                    <select v-model="userPercentageDiscount">
                                        <option :value="false">Flat Discount</option>
                                        <option :value="true">Percentage Discount</option>
                                    </select>
                                    <p class="text-bold text-xs my-1 text-dark">Discount: <i
                                            class="bi bi-currency-dollar"></i> {{ discountAmount }} </p>
                                    <div v-if="!userPercentageDiscount">
                                        <span class="text-xxs">Flat Discount:</span>
                                        <input type="number" v-model="flatDiscount" class="form-control w-40" min="0" />
                                        <p><button @click="applyDiscount"
                                                class="btn btn-warning btn-sm my-1 bg-gradient-primary w-40">Apply
                                                Flat Discount</button></p>
                                    </div>
                                    <div v-else>
                                        <span class="text-xxs">Discount (%):</span>
                                        <input type="number" v-model="discountPercentage" class="form-control w-40"
                                            min="0" max="100" step="0.25" />
                                        <p><button @click="applyDiscount"
                                                class="btn btn-warning btn-sm my-1 bg-gradient-primary w-40">Apply
                                                Percentage Discount</button></p>
                                    </div>
                                    <p><button @click="removeDiscount"
                                            class="btn btn-secondary btn-sm my-1 bg-gradient-primary w-40">Remove
                                            Discount</button></p>

                                    <hr class="mx-0 my-2 p-0 bg-secondary" />
                                    <p class="text-bold text-xs my-1 text-dark">Payable: <i
                                            class="bi bi-currency-dollar"></i> {{ payable }}</p>
                                    <p><button @click="createInvoice"
                                            class="btn btn-success btn-sm my-3 bg-gradient-primary w-40">Confirm</button>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Selection -->
            <div class="col-md-4 col-lg-4 p-2">
                <div class="card">
                    <div class="card-body">
                        <h4>Select Product</h4>
                        <input v-model="searchProductValue" placeholder="Search..."
                            class="form-control mb-2 w-auto form-control-sm" type="text" />
                        <EasyDataTable buttons-paginations alternating :items="ProductItem" :headers="ProductHeader"
                            :rows-per-page="10" :search-value="searchProductValue" :seach-field="searchProductField"
                            show-index>

                            <template #item-image="{ image }">
                                <img :src="image ? image : 'placeholderimage.png'" alt="Product Image" height="40px"
                                    width="40px" />
                            </template>

                            <template #item-action="{ id, image, name, price, unit }">
                                <button @click="addProductToSale(id, image, name, price, unit)"
                                    :class="['btn btn-sm', unit > 0 ? 'btn-success' : 'btn-warning']">
                                    {{ unit > 0 ? 'Add' : 'Stock out' }}
                                </button>
                            </template>
                        </EasyDataTable>
                    </div>
                </div>
                {{ selectedProduct }}
            </div>

            <!-- Customer Selection -->
            <div class="col-md-4 col-lg-4 p-2">
                <div class="card">
                    <div class="card-body">
                        <h4>Select Customer</h4>
                        <input placeholder="Search..." class="form-control mb-2 w-auto form-control-sm" type="text"
                            v-model="searchCustomerValue" />
                        <EasyDataTable buttons-pagination alternating :headers="CustomerHeader" :items="CustomerItem"
                            show-index :rows-per-page="10" :search-field="searchCustomerField"
                            :search-value="searchCustomerValue">
                            <template #item-action="{ id, name, email }">
                                <button @click="addCustomerToSale({ id, name, email })"
                                    class="btn btn-success btn-sm">Add</button>
                            </template>
                        </EasyDataTable>
                    </div>
                </div>
                {{ selectedCustomer }}
            </div>
        </div>
    </div>
</template>

<script setup>

import { useForm, usePage, router } from '@inertiajs/vue3';
import { createToaster } from '@meforma/vue-toaster';
import { ref, watch } from 'vue';

const toaster = createToaster({ position: 'top-right' });
let page = usePage();

const selectedCustomer = ref(null);

const CustomerHeader = [
    { text: 'Name', value: 'name' },
    { text: 'Action', value: 'action' }
];

const CustomerItem = ref(page.props.customers);
const searchCustomerField = ref(['name']);
const searchCustomerValue = ref('');

const addCustomerToSale = (customer) => {
    selectedCustomer.value = customer;
};

const selectedProduct = ref([]);
const ProductHeader = [
    { text: 'Image', value: 'image' },
    { text: 'Name', value: 'name' },
    { text: 'Qty', value: 'unit' },
    { text: 'Action', value: 'action' }
];

const ProductItem = ref(page.props.products);
const searchProductField = ref(['name']);
const searchProductValue = ref('');

const addProductToSale = (id, image, name, price, productUnit) => {
    const existingProduct = selectedProduct.value.find(product => product.id === id);
    if (existingProduct) {
        if (existingProduct.existQty > 0) {
            existingProduct.unit++;
            existingProduct.existQty--;
            calculateTotal();
        } else {
            toaster.warning(`${name} is out of stock!`);
        }

    } else {
        if (productUnit > 0) {
            const product = {
                id: id,
                image: image,
                name: name,
                price: price,
                unit: 1,
                existQty: productUnit - 1,
                productPrice: price
            };
            selectedProduct.value.push(product);
            calculateTotal();
        } else {
            toaster.warning(`${name} is out of stock!2`);
        }
    }
};

const addQty = (id) => {
    const product = selectedProduct.value.find(product => product.id === id);
    if (product.existQty > 0) {
        product.unit++;
        product.existQty--;
        calculateTotal();
    } else {
        toaster.warning(`${product.name} is out of stock!`);
    }
};

const removeQty = (id) => {
    const product = selectedProduct.value.find(product => product.id === id);
    if (product.unit > 1) {
        product.unit--;
        product.existQty++;
        calculateTotal();
    }
};

const removeProductFromSale = (index) => {
    selectedProduct.value.splice(index, 1);
    toaster.success('Product removed from sale');
    calculateTotal();
    calculatePayable();
    removeVat();
    removeDiscount();
};

const vatRate = ref(5);
const vatAmount = ref(0);
const discountAmount = ref(0);
const flatDiscount = ref(0);
const discountPercentage = ref(0);
const total = ref(0);
const userPercentageDiscount = ref(false);

const calculateTotal = () => {
    return selectedProduct.value.reduce((sum, product) => sum + (product.productPrice * product.unit), 0);
};

const applyVat = () => {
    vatAmount.value = (calculateTotal() * vatRate.value) / 100;
    calculatePayable();
};

const vatRemove = () => {
    vatAmount.value = 0;
    calculatePayable();
};

const applyDiscount = () => {
    if (userPercentageDiscount.value) {
        discountAmount.value = (calculateTotal() * discountPercentage.value) / 100;
    } else {
        discountAmount.value = flatDiscount.value;
    }
    calculatePayable();
};

const removeDiscount = () => {
    discountAmount.value = 0;
    flatDiscount.value = 0;
    discountPercentage.value = 0;
    calculatePayable();
};

const calculatePayable = () => {
    const totalAmount = calculateTotal();
    payable.value = totalAmount + vatAmount.value - discountAmount.value;
};

watch(
    [selectedProduct, vatAmount, discountAmount],
    () => {
        calculatePayable();
    },
    { deep: true }
)

const payable = ref(0);

const form = useForm({
    customer_id: '',
    products: '',
    vat: '',
    discount: '',
    total: calculateTotal(),
    payable: ''
});

const createInvoice = () => {
    if (!selectedCustomer.value) {
        toaster.error('Please select a customer');
        return;
    }
    if (selectedProduct.value.length === 0) {
        toaster.error('Please select at least one product');
        return;
    }

    form.products = selectedProduct.value;
    form.customer_id = selectedCustomer.value.id;
    form.total = total.value;
    form.vat = vatAmount.value;
    form.discount = discountAmount.value;
    form.payable = payable.value;
    form.calculateTotal = calculateTotal();

    form.total = calculateTotal();

    form.post('/invoice_create', {
        onSuccess: () => {
            if (page.props.flash.status == true) {
                toaster.success(page.props.flash.message);
                setTimeout(() => {
                    router.get('/invoice_list_page');
                }, 500);
            } else {
                toaster.error(page.props.flash.message);
            }
        },
        onError: (errors) => {
            toaster.error('Failed to create invoice');
            console.log(errors);
        }
    });
};

</script>
